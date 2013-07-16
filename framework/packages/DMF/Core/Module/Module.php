<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Module;

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

        /** Конструктор */
        public function __construct($module_name, $module_namespace)
        {
            $this->name = $module_name;
            $this->namespace = $module_namespace;
            $this->path = $this->resolve_path($module_namespace) . _SEP;
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
