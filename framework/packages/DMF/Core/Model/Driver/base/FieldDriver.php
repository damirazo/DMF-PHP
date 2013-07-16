<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Driver\base;

    /**
     * Class FieldDriver
     * Абстрактный класс для описания драйвера полей
     *
     * @package DMF\Core\Model\Driver\base
     */
    abstract class FieldDriver
    {

        protected $field;

        public function __construct($field)
        {
            $this->field = $field;
        }

        public function get_sql()
        {
            return false;
        }

    }