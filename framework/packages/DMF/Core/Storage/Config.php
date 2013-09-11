<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Storage;

    use DMF\Core\Storage\Exception\IsFinalElement;
    use DMF\Core\Storage\Element;

    /**
     * Class Config
     * Хранение конфигурационных параметров
     *
     * @package DMF\Core\Storage
     */
    class Config
    {

        /** @var array Массив для хранения данных */
        private static $_data = [];

        /**
         * Создание элемента конфигурации
         * @param string $name  Имя элемента конфигурации
         * @param mixed  $value Содержимое элемента конфигурации
         * @param bool   $final Является ли элемент "финальным" (можно ли его переопределить)
         * @throws \DMF\Core\Storage\Exception\IsFinalElement
         */
        public static function set($name, $value, $final = false)
        {
            if (isset(self::$_data[$name])) {
                /** @var $element \DMF\Core\Storage\Element */
                $element = self::$_data[$name];
                if ($element->is_final()) {
                    throw new IsFinalElement('Элемент с именем ' . $name
                            . ' уже был объявлен как финальный и его нельзя переопределить!');
                }
            }
            self::$_data[$name] = new Element($name, $value, $final);
        }

        /**
         * Получение элемента конфигурации
         * @param  string  $name    Имя элемента конфигурации
         * @param  mixed   $default Значение, возвращаемое по умолчанию
         * @return mixed
         */
        public static function get($name, $default = false)
        {
            // Если элемент с именем $name задан, то возвращаем его
            // В противном случае возвращаем значение $default
            if (self::has($name)) {
                /** @var $element \DMF\Core\Storage\Element */
                $element = self::$_data[$name];
                return $element->get();
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
