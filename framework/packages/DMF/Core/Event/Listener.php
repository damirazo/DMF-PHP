<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Event;

    use DMF\Core\Application\Application;
    use DMF\Core\Application\Exception\BaseException;
    use DMF\Core\Component\Component;
    use DMF\Core\OS\OS;
    use DMF\Core\Application\Exception\IllegalArgument;

    /**
     * Class Listener
     * Объект "слушатель" событий
     *
     * @package DMF\Core\Event
     */
    class Listener extends Component
    {

        /** @var string Имя текущего слушателя */
        protected $name;
        /** @var string|callback Вызываемое при инициализации действие */
        protected $callback;
        /** @var null|callable */
        protected $callable_class_instance = null;

        /**
         * Инициализация слушателя
         * @param string          $name Имя события
         * @param string|callback $callback Вызываемое при инициализации события действие
         */
        public function __construct($name, $callback)
        {
            $this->name = $name;
            $this->callback = $callback;
        }

        /**
         * Вызов текущего слушателя
         * @param array $params Список переданных параметров
         * @throws \DMF\Core\Application\Exception\IllegalArgument
         * @return mixed
         */
        public function call($params = [])
        {
            list($module_name, $class_name, $action_name) = $this->parse_callable($this->callback);
            $module = self::$app->get_module_by_name($module_name);
            $class_namespace = sprintf('%s\\Event\\%s', $module->namespace, $class_name);
            $class = Event::get_or_create_class($class_namespace);
            return $class->{$action_name}($params);
        }

    }
