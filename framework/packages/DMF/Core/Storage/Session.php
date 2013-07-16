<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Storage;

    /**
     * Class Session
     * Класс для работы с сессиями
     *
     * @package DMF\Core\Storage
     */
    class Session
    {

        /** @var mixed Инстанс объекта */
        private static $_instance = null;

        /** Запрещаем создание объекта через конструктор */
        private function __construct()
        {
        }

        /**
         * Возвращаем инстанс объекта
         * @return Session|mixed|null
         */
        public static function get_instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new Session();
            }

            return self::$_instance;
        }

        /**
         * Проверка запущены ли сессии
         * @return bool
         */
        public function is_active()
        {
            return !!(session_id() != '');
        }

        /**
         * Проверка наличия элемента в сессии
         * @param string $name Имя элемента
         *
         * @return bool
         */
        public function has($name)
        {
            return !!(isset($_SESSION[$name]));
        }

        /**
         * Получение элемента сессии
         * @param string $name    Имя элемента
         * @param mixed  $default Значение по умолчанию
         *
         * @return bool
         */
        public function get($name, $default = false)
        {
            if ($this->has($name)) {
                return $_SESSION[$name];
            }

            return $default;
        }

        /**
         * Задание элемента сессии
         * @param string $name  Имя элемента
         * @param mixed  $value Содержимое элемента
         */
        public function set($name, $value)
        {
            $_SESSION[$name] = $value;
        }

        /**
         * Удаление элемента сессии
         * @param string $name Имя элемента
         */
        public function remove($name)
        {
            if ($this->has($name)) {
                unset($_SESSION[$name]);
            }
        }

        /**
         * Задание одноразового элемента
         * @param string $name  Имя элемента
         * @param mixed  $value Содержимое элемента
         */
        public function set_flash($name, $value)
        {
            $this->set('_flash_' . $name, $value);
        }

        /**
         * @param      $name
         * @param bool $default
         *
         * @return bool
         */
        public function get_flash($name, $default = false)
        {
            $flash_name = '_flash_' . $name;
            $data = $this->get($flash_name, $default);
            $this->remove($flash_name);

            return $data;
        }

    }
