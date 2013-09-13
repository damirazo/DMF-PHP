<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Application;

    /**
     * Class Route
     * Класс для хранения информации о маршруте
     *
     * @package DMF\Core\Application
     */
    class Route
    {

        /** @var string URL маршрут */
        public $url;
        /** @var \DMF\Core\Application\RoutePattern Путь до действия */
        public $pattern;
        /** @var array Список переданных аргументов */
        public $arguments;

        /**
         * Инициализация объекта
         * @param string                             $url URL маршрут
         * @param \DMF\Core\Application\RoutePattern $pattern Путь до действия
         * @param array                              $arguments Список переданных аргументтов
         */
        public function __construct($url, $pattern, $arguments = [])
        {
            $this->url = $url;
            $this->pattern = $pattern;
            $this->arguments = $arguments;
        }

    }