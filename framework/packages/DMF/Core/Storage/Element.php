<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Storage;

    /**
     * Class Element
     * Элемент настроек
     *
     * @package DMF\Core\Storage
     */
    class Element
    {

        /** @var null|string Имя элемента */
        protected $name = null;

        /** @var null|array|string|int|bool Значение элемента */
        protected $data = null;

        /** @var bool Является ли этот элемент финальным (можно ли его переопределить?) */
        protected $final = false;

        /** Конструктор объекта */
        public function __construct($name, $data, $final = false)
        {
            $this->name = $name;
            $this->data = $data;
            $this->final = $final;
        }

        /** Получение содержимого объекта */
        public function get()
        {
            return $this->data;
        }

        /** Проверка является ли элемент "финальным" */
        public function is_final()
        {
            return !!($this->final);
        }

    }
