<?php

    namespace DMF\Core\Application;

    use DMF\Core\Router\Router;
    use DMF\Core\Storage\Config;
    use DMF\Core\Http\Request;
    use DMF\Core\Module\Module;
    use DMF\Core\OS\OS;
    use DMF\Core\Event\Event;
    use DMF\Core\Http\Exception\Http404;
    use DMF\Core\Application\Exception\ModuleNotFound;
    use DMF\Core\Application\Exception\ControllerProxyNotFound;

    /**
     * Базовый класс приложения
     */
    class Application
    {

        /** @var \DMF\Core\Http\Request */
        private $request;

        /** @var null|Application Инстанс объекта */
        private static $_instance = null;

        /** @var null|array Объект с данными о текущем маршруте */
        public $route_object = null;

        /** @var array Список модулей */
        public $modules = [];

        /** @var null|\DMF\Core\Module\Module */
        public $module = null;

        /** Запрет на создание объекта */
        private function __construct()
        {
        }

        /** Запрет на копирование объекта */
        private function __clone()
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
         * Регистрация списка модулей
         * @param array $modules Списрк модулей
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
         * Возвращает имя требуемого модуля
         * @return string
         */
        protected function module_name()
        {
            return $this->parse_route()['pattern']->module_name;
        }

        /**
         * Разбивка URI и получение информации о маршруте
         * @return array
         * @throws \DMF\Core\Http\Exception\Http404
         */
        protected function parse_route()
        {
            // Проверяем не был ли текущий объект маршрута уже закеширован ранее
            if (is_null($this->route_object)) {
                // Текущая строка запроса
                $uri = $this->request->request_uri();
                // Список зарегистрированных маршрутов
                $routes = Router::data();
                // Поиск совпадающих со строкой запроса маршрутов
                foreach ($routes as $regexp => $pattern) {
                    // Заменяем псевдонимы для regexp шаблона
                    $regexp = str_replace(
                        ['@int', '@str', '@alphanum', '@all'],
                        ['[\d]+', '[\w]+', '[\w\d]+', '[\w\d\.\,\-\_\+]+'],
                        $regexp
                    );
                    // Проверяем соответствие текущей строке запроса и паттерну маршрута
                    if (preg_match('~^' . $regexp . '$~i', $uri, $arguments)) {
                        $route_object = [
                            'url'       => $this->request->request_uri(),
                            'pattern'   => $pattern,
                            'arguments' => array_slice($arguments, 1)
                        ];
                        $this->route_object = $route_object;
                        return $route_object;
                    }
                }
                throw new Http404('Страница ' . Request::get_instance()->url() . ' отсутствует на сайте!');
            }
            else {
                return $this->route_object;
            }
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
                }
                else {
                    throw new ModuleNotFound('Модуль ' . $module_name . ' не найден по указанному пути!');
                }
            }
            else {
                throw new ModuleNotFound('Модуль ' . $module_name . ' не зарегистрирован!');
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
         * Загрузка кастомных настроек
         */
        protected function load_config()
        {
            // Загрузка системного файла настроек
            OS::import(CONFIG_PATH . 'config.php');
            // Загрузка файла настроек модуля
            foreach ($this->modules as $module_name => $module_namespace) {
                $module = $this->get_module_by_name($module_name);
                OS::import($module->path . 'config.php', false);
            }
        }

        /**
         * Загрузка событий
         */
        protected function load_events()
        {
            // Загрузка системного файла событий
            OS::import(CONFIG_PATH . 'events.php');
            // Загрузка событий всех зарегистрированных модулей
            foreach ($this->modules as $module_name => $module_namespace) {
                $module = $this->get_module_by_name($module_name);
                OS::import($module->path . 'events.php', false);
            }
        }

        /**
         * Загрузка шаблонного контекста
         */
        protected function load_context()
        {
            // Загрузка системного файла шаблонного контекста
            OS::import(CONFIG_PATH . 'context.php');
            // Загрузка файла шаблонного контекста модуля
            foreach ($this->modules as $module_name => $module_namespace) {
                $module = $this->get_module_by_name($module_name);
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
            $controller_namespace = $this->module->namespace .
                    '\\Controller\\' . $route_object['pattern']->controller_name;
            /** @var $controller \DMF\Core\Controller\Controller */
            $controller = new $controller_namespace();
            if (method_exists($controller, 'proxy')) {
                return $controller->proxy($route_object['pattern']->action_name, $route_object['arguments']);
            }
            else {
                throw new ControllerProxyNotFound('Для контроллера ' . $controller_namespace
                        . ' не задан прокси-метод!');
            }
        }

        /**
         * Активация приложения
         */
        public function run()
        {
            // Получаем request объект
            $this->request = Request::get_instance();
            // Получаем данные о маршруте
            $this->parse_route();
            // Загрузка модуля
            $this->load_module();
            // Загрузка настроек
            $this->load_config();
            // Загрузка событий
            $this->load_events();
            // Загрузка шаблонного контекста
            $this->load_context();
            // Генерация события загрузки системы
            Event::trigger('boot');
            // Загрузка контроллера и действия
            $this->load_controller();
        }

    }
