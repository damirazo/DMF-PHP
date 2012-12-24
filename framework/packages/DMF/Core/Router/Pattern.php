<?php

    namespace DMF\Core\Router;

    use DMF\Core\Application\Exception\WrongArgsNumber;

    /**
     * Объектное представление для URI паттерна
     */
    class Pattern
    {

        /** @var string Имя модуля */
        public $module_name;

        /** @var string Имя контроллера */
        public $controller_name;

        /** @var string Имя действия */
        public $action_name;

        /** Конструктор */
        public function __construct($callable)
        {
            $this->parse_callable($callable);
        }

        /**
         * @param $callable
         * @throws \DMF\Core\Application\Exception\WrongArgsNumber
         */
        private function parse_callable($callable)
        {
            $data = explode('.', $callable);
            if (count($data) == 3) {
                $this->module_name = $data[0];
                $this->controller_name = $data[1];
                $this->action_name = $data[2];
            }
            else {
                throw new WrongArgsNumber('Неверный формат строки вызова контроллера! ' .
                        'Правильный формат: "module.controller.action"');
            }
        }

    }
