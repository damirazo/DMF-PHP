<?php

    namespace DMF\Core\Model\Driver\base;

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