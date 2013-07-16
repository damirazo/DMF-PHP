<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Event;

    use DMF\Core\Component\Component;

    /**
     * Class Event
     * Базовый объект событий
     *
     * @package DMF\Core\Event
     */
    class Event extends Component
    {

        /** @var array Список зарегистрированных событий */
        private static $_events = [];

        /**
         * Регистрация нового слушателя событий
         * @param string $event_name Имя события
         * @param string $callable Имя класса и метода события
         */
        public static function on($event_name, $callable)
        {
            self::$_events[$event_name][] = new Listener($callable);
        }

        /**
         * Активация события
         * @param string $event_name Имя события
         */
        public static function trigger($event_name)
        {
            if (isset(self::$_events[$event_name]) && count(self::$_events[$event_name]) > 0) {
                /** @var $listener \DMF\Core\Event\Listener */
                foreach (self::$_events[$event_name] as $listener) {
                    $listener->load();
                }
            }
        }

    }
