<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Application;

    use DMF\Core\Application\Exception\IllegalArgument;

    /**
     * Class RoutePattern
     * Объект маршрута
     *
     * @package DMF\Core\Application
     */
    class RoutePattern
    {

        /** @var null|string Путь до указанного экшена в формате Модуль.Контроллер.Экшен */
        public $callable = null;
        /** @var null Маршрут, связанный с указанным действием */
        public $pattern = null;
        /** @var string Имя модуля */
        public $module_name;
        /** @var string Имя контроллера */
        public $controller_name;
        /** @var string Имя действия */
        public $action_name;

        /** Конструктор */
        public function __construct($callable, $pattern)
        {
            $this->callable = $callable;
            $this->pattern = $pattern;
            $this->parse_callable();
        }

        /**
         * Парсинг переданного в качестве экшена значения
         * @throws \DMF\Core\Application\Exception\IllegalArgument
         */
        private function parse_callable()
        {
            $data = explode('.', $this->callable);
            if (count($data) == 3) {
                $this->module_name = $data[0];
                $this->controller_name = $data[1];
                $this->action_name = $data[2];
            } else {
                throw new IllegalArgument(
                    'Неверный формат строки вызова контроллера! ' .
                    'Правильный формат: "module.controller.action"');
            }
        }

    }
