<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Event;

    use DMF\Core\Component\Component;
    use DMF\Core\Event\Exception\EventExists;

    /**
     * Class Event
     * Базовый объект событий
     *
     * @package DMF\Core\Event
     */
    class Event
    {

        /** @var array Список зарегистрированных событий */
        private static $listeners = [];

        /**
         * Регистрация события с указанным именен и привязка к нему коллбека
         * @param string          $name Имя события
         * @param string|callable $callback Вызываемое действие
         * @throws \DMF\Core\Event\Exception\EventExists
         */
        public static function register($name, $callback)
        {
            if (isset(self::$listeners[$name])) {
                throw new EventExists(sprintf('Событие с именем %s уже было ранее зарегистрировано!', $name));
            }
            self::$listeners[$name] = new Listener($name, $callback);
        }

        /**
         * Вызов события с указанным именем
         * @param string $name Имя события
         * @param array  $params Список параметров, передаваемых слушателю события
         * @return mixed
         */
        public static function send($name, $params = [])
        {
            $listener = self::get_listener_by_name($name);
            if ($listener) {
                return $listener->call($params);
            }
            return $listener;
        }

        /**
         * Возвращает слушателя события по его имени
         * @param string $name Имя слушателя события
         * @return bool|\DMF\Core\Event\Listener
         */
        protected static function get_listener_by_name($name)
        {
            return isset(self::$listeners[$name]) ? self::$listeners[$name] : false;
        }

    }
