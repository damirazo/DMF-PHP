<?php

    namespace DMF\Core\Event;

    use DMF\Core\Application\Application;
    use DMF\Core\OS\OS;
    use DMF\Core\Application\Exception\WrongArgsNumber;

    /**
     * Слушатель событий
     */
    class Listener
    {

        /** @var string */
        private $module;

        /** @var string Имя класса события */
        private $event_class;

        /** @var string Имя метода события */
        private $event_method;

        /** Конструктор */
        public function __construct($callable)
        {
            $app = Application::get_instance();
            $data = explode('.', $callable);
            if (count($data) == 3) {
                $this->module = $app->get_module_by_name($data[0]);
                $this->event_class = $data[1];
                $this->event_method = $data[2];
            }
            elseif (count($data) == 2) {
                $this->module = $app->module;
                $this->event_class = $data[0];
                $this->event_method = $data[1];
            }
            else {
                throw new WrongArgsNumber('Неверный формат записи вызова события!
                    Требуется "[ИмяМодуля.]ИмяКласса.имяМетода", получено ' . $callable);
            }
        }

        /**
         * Загрузка события
         */
        public function load()
        {
            if (OS::file_exists($this->module->path . 'Event' . _SEP . $this->event_class . '.php')) {
                $event_namespace = $this->module->namespace . '\\Event\\' . $this->event_class;
                $event = new $event_namespace();
                if (method_exists($event, $this->event_method)) {
                    return $event->{$this->event_method}();
                }
            }
            return false;
        }

    }
