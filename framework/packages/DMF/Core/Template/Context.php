<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Template;

    use DMF\Core\Template\Exception\ContextElementExists;

    /**
     * Class Context
     * Шаблонный контекст
     *
     * @package DMF\Core\Template
     */
    class Context
    {

        /** @var array Массив шаблонных данных */
        private static $_context = [];

        /**
         * Добавление нового элемента
         * @param string                        $name  Имя элемента
         * @param string|array|int|boolean|null $value Значение элемента
         * @throws ContextElementExists
         */
        public static function add($name, $value)
        {
            if (!isset(self::$_context[$name])) {
                self::$_context[$name] = $value;
            }
            else {
                throw new ContextElementExists('Шаблонный контекст с именем ' . $name . ' уже был зарегистрирован!');
            }
        }

        /**
         * Возвращает массив с шаблонным контекстом
         * @return array
         */
        public static function data()
        {
            return self::$_context;
        }

    }
