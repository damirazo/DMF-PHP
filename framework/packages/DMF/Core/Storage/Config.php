<?php

    namespace DMF\Core\Storage;

    /**
     * Хранение конфигурационных настроек
     */
    class Config
    {

        /** @var array Массив для хранения данных */
        private static $_data = [];

        /**
         * Создание элемента конфигурации
         * @param string $name  Имя элемента конфигурации
         * @param mixed  $value Содержимое элемента конфигурации
         */
        public static function set($name, $value)
        {
            self::$_data[$name] = $value;
        }

        /**
         * Получение элемента конфигурации
         * @param  string $name    Имя элемента конфигурации
         * @param mixed   $default Значение, возвращаемое по умолчанию
         * @return mixed
         */
        public static function get($name, $default = false)
        {
            // Если элемент с именем $name задан, то возвращаем его
            // В противном случае возвращаем значение $default
            if (self::has($name)) {
                return self::$_data[$name];
            }
            return $default;
        }

        /**
         * Проверка наличия элемента конфигурации
         * @param string $name Имя элемента конфигурации
         * @return bool
         */
        public static function has($name)
        {
            return !!(isset(self::$_data[$name]));
        }

        /**
         * Удаление элемента конфигурации
         * @param string $name Имя элемента конфигурации
         */
        public static function remove($name)
        {
            if (self::has($name)) {
                unset(self::$_data[$name]);
            }
        }

        /**
         * Read Only доступ к массиву с конфигурационными настройками
         * @return array
         */
        public static function data()
        {
            return self::$_data;
        }

    }
