<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Application;

    use DMF\Core\Application\Exception\ControllerProxyNotFound;
    use DMF\Core\Application\Exception\ModuleNotFound;
    use DMF\Core\Application\RoutePattern;
    use DMF\Core\Event\Event;
    use DMF\Core\Http\Exception\Http404;
    use DMF\Core\Http\Request;
    use DMF\Core\Module\Module;
    use DMF\Core\OS\OS;
    use DMF\Core\Application\Exception\RouteExists;
    use DMF\Core\Storage\Config;

    /**
     * Class Application
     * Базовый класс приложения
     *
     * @package DMF\Core\Application
     */
    class Application
    {

        /** @var null|Application Инстанс объекта */
        private static $_instance = null;
        /** @var array Список зарегистрированных маршрутов */
        private static $_routes = [];
        /** @var null|array Объект с данными о текущем маршруте */
        public $route_object = null;
        /** @var array Список модулей */
        public $modules = [];
        /** @var null|\DMF\Core\Module\Module */
        public $module = null;
        /** @var \DMF\Core\Http\Request */
        private $request;

        /** Запрет на создание объекта */
        private function __construct()
        {
        }

        /**
         * Возвращает инстанс объекта
         */
        public static function get_instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new Application();
            }
            return self::$_instance;
        }

        /**
         * Регистрация списка маршрутов
         * @param array $routes Список маршрутов
         * @throws \DMF\Core\Application\Exception\RouteExists
         */
        public static function routes($routes = [])
        {
            foreach ($routes as $pattern => $callable) {
                if (!isset(self::$_routes[$pattern])) {
                    self::$_routes[$pattern] = new RoutePattern($callable);
                } else {
                    throw new RouteExists(
                        sprintf('Маршрут %s уже был ранее задан для действия %s', $pattern, $callable));
                }
            }
        }

        /**
         * Регистрация списка модулей
         * @param array $modules Список модулей
         */
        public function register_modules(array $modules = [])
        {
            $data = [];
            foreach ($modules as $module_name => $module_namespace) {
                $data[$module_name] = new Module($module_name, $module_namespace);
            }
            $this->modules = $data;
        }

        /**
         * Активация приложения
         */
        public function run()
        {
            // Получаем request объект
            $this->request = Request::get_instance();
            // Активация фреймворка через консоль
            // для работы через командный интерфейс
            if (self::is_cli()) {
                $this->loader();
                // Генерируем событие, что мы загрузились через консоль
                // Все остальные параметры нам не нужны
                Event::trigger('cli');
            } else {
                // Получаем данные о маршруте
                $this->parse_route();
                // Загрузка модуля
                $this->load_module();
                // Загрузка системных параметров
                $this->loader();
                // Генерация события загрузки системы
                Event::trigger('app_ready');
                // Загрузка контроллера и действия
                $this->load_controller();
            }
        }

        /**
         * Проверка на то, запущен ли фреймворк через консоль
         * @return bool
         */
        public static function is_cli()
        {
            return PHP_SAPI === 'cli';
        }

        /**
         * Разбивка URI и получение информации о маршруте
         *
         * @return array
         * @throws \DMF\Core\Http\Exception\Http404
         */
        protected function parse_route()
        {
            // Список замен ключей на соответствующие куски regexp шаблонов
            $replace_routes_patterns = [
                '@int'      => '[\d]+',
                '@str'      => '[\w]+',
                '@alphanum' => '[\w\d]+',
                '@all'      => '[\w\d\.\,\-\_\+]+',
            ];
            // Проверяем не был ли текущий объект маршрута уже закеширован ранее
            if (is_null($this->route_object)) {
                // Текущая строка запроса
                $uri = $this->request->request_uri();
                // Список зарегистрированных маршрутов
                // Поиск совпадающих со строкой запроса маршрутов
                foreach (self::$_routes as $regexp => $pattern) {
                    // Заменяем псевдонимы для regexp шаблона
                    foreach ($replace_routes_patterns as $k => $v) {
                        $regexp = str_replace($k, $v, $regexp);
                    }
                    // Проверяем соответствие текущей строке запроса и паттерну маршрута
                    if (preg_match('~^' . $regexp . '$~i', $uri, $arguments)) {
                        $route_object = [
                            'url'       => $this->request->request_uri(),
                            'pattern'   => $pattern,
                            'arguments' => array_slice($arguments, 1),
                        ];
                        $this->route_object = $route_object;
                        return $route_object;
                    }
                }
                throw new Http404('Страница ' . Request::get_instance()->url() . ' отсутствует на сайте!');
            } else {
                return $this->route_object;
            }
        }

        /**
         * Загрузка модуля и информации о нем
         * @throws Exception\ModuleNotFound
         */
        protected function load_module()
        {
            $this->module = $this->get_module_by_name();
        }

        /**
         * Создание объекта модуля
         * @param null|string $name Имя модуля
         * @return \DMF\Core\Module\Module
         * @throws Exception\ModuleNotFound
         */
        public function get_module_by_name($name = null)
        {
            $module_name = is_null($name) ? $this->module_name() : $name;
            // Проверяем регистрацию модуля
            if (isset($this->modules[$module_name])) {
                $module = $this->modules[$module_name];
                // Проверяем существование папки с модулем
                if (OS::dir_exists($module->path)) {
                    return $module;
                } else {
                    throw new ModuleNotFound('Модуль ' . $module_name . ' не найден по указанному пути!');
                }
            } else {
                throw new ModuleNotFound('Модуль ' . $module_name . ' не зарегистрирован!');
            }
        }

        /**
         * Возвращает объект требуемого компонента
         * @param string $name Имя компонента
         * @param string $type Тип компонента. Типы перечислены в классе DMF\Core\Component\ComponentTypes.
         * @return mixed
         */
        public function get_component($name, $type)
        {
            $data = explode('.', $name);
            $module = $this->get_module_by_name($data[0]);
            $component_name = $data[1];
            $component_namespace = $module->namespace . '\\' . $type . '\\' . $component_name;
            return new $component_namespace();
        }

        /**
         * Возвращает имя требуемого модуля
         * @return string
         */
        protected function module_name()
        {
            return $this->parse_route()['pattern']->module_name;
        }

        /**
         * Загрузка системных параметров
         */
        protected function loader()
        {
            // Загрузка глобальных настроек
            OS::import(CONFIG_PATH . 'config.php');
            // Загрузка глобальных событий
            OS::import(CONFIG_PATH . 'events.php');
            // Загрузка глобального шаблонного контекста
            OS::import(CONFIG_PATH . 'context.php');

            // Обход списка зарегистрированных модулей и загрузка их параметров
            foreach ($this->modules as $module_name => $module_namespace) {
                $module = $this->get_module_by_name($module_name);
                OS::import($module->path . 'config.php', false);
                OS::import($module->path . 'events.php', false);
                OS::import($module->path . 'context.php', false);
            }
        }

        /**
         * Загрузка контроллера
         * @return mixed
         * @throws Exception\ControllerProxyNotFound
         */
        protected function load_controller()
        {
            $route_object = $this->route_object;
            $controller_namespace = $this->module->namespace . '\\Controller\\' . $route_object['pattern']->controller_name;
            /** @var $controller \DMF\Core\Controller\Controller */
            $controller = new $controller_namespace();
            if (method_exists($controller, 'proxy')) {
                return $controller->proxy($route_object['pattern']->action_name, $route_object['arguments']);
            } else {
                throw new ControllerProxyNotFound('Для контроллера ' . $controller_namespace . ' не задан прокси-метод!');
            }
        }

        /** Запрет на копирование объекта */
        private function __clone()
        {
        }

    }
