<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Application;

    use DMF\Core\Application\Exception\ControllerProxyNotFound;
    use DMF\Core\Application\Exception\IllegalArgument;
    use DMF\Core\Application\Exception\IncorrectResponseFormat;
    use DMF\Core\Application\Exception\ModuleNotFound;
    use DMF\Core\Autoloader\Autoloader;
    use DMF\Core\Event\Event;
    use DMF\Core\Http\Exception\Http404;
    use DMF\Core\Http\Request;
    use DMF\Core\Http\Response;
    use DMF\Core\Module\Module;
    use DMF\Core\OS\File;
    use DMF\Core\OS\OS;
    use DMF\Core\Application\Exception\RouteExists;

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
        /** @var array Список зарегистрированных маршрутов с ключом в виде пути до действия */
        private static $_routes_by_path = [];
        /** @var array Список созданных экземпляров компонентов */
        private static $_component_cache = [];
        /** @var null|\DMF\Core\Application\Route Объект с данными о текущем маршруте */
        public $route = null;
        /** @var array Список модулей */
        public static $modules = [];
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
        public static function register_routes($routes = [])
        {
            foreach ($routes as $pattern => $callable) {
                if (!isset(self::$_routes[$pattern])) {
                    $route = new RoutePattern($callable, $pattern);
                    self::$_routes[$pattern] = $route;
                    self::$_routes_by_path[$callable] = $route;
                } else {
                    throw new RouteExists(
                        sprintf('Маршрут %s уже был ранее задан для действия %s', $pattern, $callable));
                }
            }
        }

        /**
         * Возвращает экземпляр объекта RoutePattern, содержащего информацию о маршруте, для указанного пути до экшена
         *
         * @param string $path Путь до экшена в формате Модуль.Контроллер.Экшен
         * @return bool|RoutePattern
         */
        public static function get_route_by_path($path)
        {
            if (isset(self::$_routes_by_path[$path])) {
                return self::$_routes_by_path[$path];
            }
            return false;
        }

        /**
         * Регистрация модуля
         * @param string $module_name Имя модуля
         * @param array  $module_data Информация о модуле
         */
        public static function register_module($module_name, $module_data)
        {
            self::$modules[$module_name] = new Module($module_name, $module_data);
        }

        /**
         * Регистрация списка модулей
         * @param array $modules
         */
        public static function register_modules($modules = [])
        {
            foreach ($modules as $module_name => $module_data) {
                self::$modules[$module_name] = new Module($module_name, $module_data);
            }
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
            } else {
                // Получаем данные о маршруте
                $this->parse_route();
                // Загрузка модуля
                $this->load_module();
                // Загрузка системных параметров
                $this->loader();
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
         * @return \DMF\Core\Application\Route
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
            if (is_null($this->route)) {
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
                        $route_object = new Route(
                            $this->request->request_uri(),
                            $pattern,
                            array_slice($arguments, 1)
                        );
                        $this->route = $route_object;
                        return $route_object;
                    }
                }
                throw new Http404('Страница ' . Request::get_instance()->url() . ' отсутствует на сайте!');
            } else {
                return $this->route;
            }
        }

        /**
         * Загрузка и компиляция маршрутов из зарегистрированных модулей
         */
        public function load_routes()
        {
            // Путь до скомпилированного файла с маршрутами
            $cache_file = DATA_PATH . 'cache' . _SEP . 'classes' . _SEP . 'routes.php';
            // Если файл с маршрутами уже существует и режим разработки отключен, то загружаем его
            if (!DEBUG && OS::file_exists($cache_file)) {
                OS::import($cache_file);
            } else {
                // Загрузка файлов с маршрутами из всех зарегистрированных модулей
                foreach (self::$modules as $module_name => $module) {
                    OS::import($module->path . 'routes.php', false);
                }
                // Запись маршрутов в файл
                $file = new File($cache_file);
                $file->open('w+');
                $file->block();
                $file->write_line('<?php');
                $file->write_line('/** Данный файл сгенерирован автоматически */');
                $file->write_line('use \DMF\Core\Application\Application;');
                $file->write_line('Application::register_routes([');
                $str = [];
                foreach (self::$_routes as $path => $route) {
                    $str[] = sprintf('"%s" => "%s",', $path, $route->callable);
                }
                $file->write($str);
                $file->write_line(']);');
                $file->unblock();
                $file->close();
            }
            return $this;
        }

        /**
         * Поиск и загрузка модулей из всех зарегистрированных пространств имен
         */
        public function load_modules()
        {
            // Путь до скомпилированного файла с модулями
            $cache_file = DATA_PATH . 'cache' . _SEP . 'classes' . _SEP . 'modules.php';
            if (!DEBUG && OS::file_exists($cache_file)) {
                OS::import($cache_file);
            } else {
                // Обходим список пространств имен и пытаемся обнаружить информацию о модулях
                $namespaces = Autoloader::get_namespaces();
                foreach ($namespaces as $root => $path) {
                    $dirs_in_path = OS::dirs($path);
                    foreach ($dirs_in_path as $dir) {
                        if (OS::file_exists($dir . _SEP . 'module.php')) {
                            OS::import($dir . _SEP . 'module.php');
                        }
                    }
                }
                // Создание скомпилированного файла с информацией о модулях
                $file = new File($cache_file);
                $file->open('w+');
                $file->block();
                $file->write_line('<?php');
                $file->write_line('/** Данный файл сгенерирован автоматически */');
                $file->write_line('use \DMF\Core\Application\Application;');
                $file->write_line('Application::register_modules([');
                $str = [];
                foreach (self::$modules as $module_name => $module) {
                    $data = [];
                    foreach ($module->data as $k => $v) {
                        $data[] = sprintf('"%s" => "%s"', $k, str_replace('\\', '\\\\', $v));
                    }
                    $str[] = sprintf('"%s" => [%s],', $module_name, implode(', ', $data));
                }
                $file->write($str);
                $file->write_line(']);');
                $file->unblock();
                $file->close();
            }
            return $this;
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
            if (isset(self::$modules[$module_name])) {
                $module = self::$modules[$module_name];
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
         * @throws \DMF\Core\Application\Exception\IllegalArgument
         */
        public function get_component($name, $type)
        {
            $data = explode('.', $name);
            if (count($data) == 1) {
                $module_name = null;
                $component_name = $data[0];
            } else if (count($data) == 2) {
                list($module_name, $component_name) = $data;
            } else {
                throw new IllegalArgument('Некорректный формат записи имени компонента!');
            }

            $module = $this->get_module_by_name($module_name);
            $component_namespace = $module->namespace . '\\' . $type . '\\' . $component_name;

            if (array_key_exists($component_namespace, self::$_component_cache)) {
                return self::$_component_cache[$component_namespace];
            } else {
                $component = new $component_namespace();
                self::$_component_cache[$component_namespace] = $component;
                return $component;
            }
        }

        /**
         * Возвращает имя требуемого модуля
         * @return string
         */
        public function module_name()
        {
            return $this->parse_route()->pattern->module_name;
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
            // Загрузка кастомных шаблонных тегов
            OS::import(CONFIG_PATH . 'template_tags.php');

            // Обход списка зарегистрированных модулей и загрузка их параметров
            foreach (self::$modules as $module_name => $module_namespace) {
                $module = $this->get_module_by_name($module_name);
                OS::import($module->path . 'config.php', false);
                OS::import($module->path . 'events.php', false);
                OS::import($module->path . 'context.php', false);
                OS::import($module->path . 'template_tags.php', false);
            }
        }

        /**
         * Загрузка контроллера
         * @throws \DMF\Core\Application\Exception\ControllerProxyNotFound
         * @throws \DMF\Core\Application\Exception\IncorrectResponseFormat
         * @return mixed
         */
        protected function load_controller()
        {
            $controller_namespace = $this->module->namespace
                . '\\Controller\\'
                . $this->route->pattern->controller_name;
            /** @var $controller \DMF\Core\Controller\Controller */
            $controller = new $controller_namespace();
            // Выбрасывание события после инициализации контроллера
            Event::trigger('DMF.controller', [
                'request'    => $this->request,
                'controller' => $controller,
            ]);
            if (method_exists($controller, 'proxy')) {
                $data = $controller->proxy($this->route->pattern->action_name, $this->route->arguments);
                Event::trigger('DMF.response', [
                    'request'  => $this->request,
                    'response' => $data,
                ]);
                if (!$data instanceof Response) {
                    throw new IncorrectResponseFormat(
                        sprintf(
                            'Действие %s не вернуло значение в формате Response!',
                            $this->route->pattern->callable));
                } else {
                    return $data;
                }
            } else {
                throw new ControllerProxyNotFound(
                    'Для контроллера ' . $controller_namespace . ' не задан прокси-метод!');
            }
        }

        /** Запрет на копирование объекта */
        private function __clone()
        {
        }

    }
