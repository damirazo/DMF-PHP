<?php

    namespace DMF\Core\Autoloader;

    use DMF\Core\Autoloader\Exception\ClassNotFound;

    /**
     * Автозагрузчик классов
     */
    class Autoloader
    {

        /** @var array Список корневых пространств имен */
        private static $_namespaces = [];

        /**
         * Регистрация корневых пространств имен
         * @param array $namepspaces Список корневых пространств имен
         */
        public static function register_namespaces($namepspaces = [])
        {
            self::$_namespaces = $namepspaces;
        }

        /**
         * Разбивка пути к классу на сегменты
         * @param string $namespace Путь к классу
         * @return array
         */
        protected static function parse_namespace($namespace)
        {
            // Реализация обратной совместимости с PSR-0 стандартом загрузки классов
            return (!mb_strpos($namespace, '\\')) ? explode('_', $namespace) : explode('\\', $namespace);
        }

        /**
         * Возвращает имя корневого пространства имен
         * @param string $namespace Путь к классу
         * @return string
         */
        protected static function get_root_namespace($namespace)
        {
            return self::parse_namespace($namespace)[0];
        }

        /**
         * Возвращает корневой путь для указанного пространства имен
         * @param string $namespace Пространство имен
         * @return string
         * @throws Exception\ClassNotFound
         */
        public static function get_root_path($namespace)
        {
            $root_namespace = self::get_root_namespace($namespace);
            foreach (self::$_namespaces as $root => $path) {
                if ($root_namespace == $root) {
                    // Корректируем на случай, если в конце пути не указан разделитель директорий
                    if (mb_substr($path, -1) != _SEP) {
                        $path = $path . _SEP;
                    }
                    return $path;
                }
            }
            throw new ClassNotFound('Корневое пространство имен ' . $root_namespace . ' не зарегистрировано!');
        }

        /**
         * Возвращает полный путь для указанного пространства имен
         * @param string $namespace Пространство имен
         * @return string
         * @throws Exception\ClassNotFound
         */
        public static function get_path($namespace)
        {
            $root_path = self::get_root_path($namespace);
            return $root_path . implode(_SEP, array_slice(self::parse_namespace($namespace), 1));
        }

        /**
         * Загрузка требуемого класса
         * @param string $namespace Путь к классу
         * @return bool
         * @throws \DMF\Core\Autoloader\Exception\ClassNotFound;
         */
        public static function load($namespace)
        {
            $class_path = self::get_path($namespace) . '.php';
            if (is_readable($class_path)) {
                require_once $class_path;
                return true;
            }
            else {
                throw new ClassNotFound('Класс ' . $namespace . ' не обнаружен или недоступен для чтения!');
            }
        }

        /**
         * Активация перехватчика ошибок
         */
        public static function run()
        {
            spl_autoload_register(__CLASS__ . '::load');
        }

    }
