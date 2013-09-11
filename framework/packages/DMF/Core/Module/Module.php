<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Module;

    use DMF\Core\Application\Application;
    use DMF\Core\Autoloader\Autoloader;

    /**
     * Class Module
     * Базовый класс описания модуля
     *
     * @package DMF\Core\Module
     */
    class Module
    {

        /** @var string Имя модуля */
        public $name;
        /** @var string Пространство имен модуля */
        public $namespace;
        /** @var string Путь до модуля */
        public $path;
        /** @var null|string Версия модуля */
        public $version = null;
        /** @var null|string Автор модуля  */
        public $author = null;
        /** @var array Полная информация о модуле */
        public $data = [];
        /** @var  \DMF\Core\Application\Application Инстанс приложения */
        protected static $app = null;

        /** Конструктор */
        public function __construct($module_name, $module_data)
        {
            $this->name = $module_name;
            $this->namespace = $module_data['namespace'];
            $this->author = isset($module_data['author']) ? $module_data['author'] : null;
            $this->version = isset($module_data['version']) ? $module_data['version'] : null;
            $this->data = $module_data;
            $this->path = $this->resolve_path($this->namespace) . _SEP;
            if (is_null(self::$app)) {
                self::$app = Application::get_instance();
            }
        }

        /**
         * Получение пути до модуля на основе его пространства имен
         * @param string $namespace Пространство имен
         * @return array
         */
        private function resolve_path($namespace)
        {
            return Autoloader::get_path($namespace);
        }

    }
