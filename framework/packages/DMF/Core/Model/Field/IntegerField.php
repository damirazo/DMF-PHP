<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * IntegerField.php
     * 21.11.12, 17:21
     */

    namespace DMF\Core\Model\Field;

    /**
     * Поле для хранения целого числа
     */
    class IntegerField extends BaseField
    {

        /**
         * Возвращает строку, содержащую код для создания данного поля
         * @param string $name Имя поля
         *
         * @return string
         *
         * Используемые параметры и их значения по умолчанию:
         * nullable: false
         * default: false
         */
        public function create_sql($name)
        {
            $nullable = ($this->get_param('nullable', false) === true) ? 'NULL' : 'NOT NULL';
            $default = $this->get_param('default', false);
            $default_value = $default === false ? '' : 'DEFAULT '.$default;

            return '`' . $name . '` INT(' . $this->get_param('length', 11) . ') '.$nullable.' '.$default_value;
        }

        /**
         * Возвращает тип поля
         * @return string
         */
        public function type()
        {
            return 'integer';
        }

        /**
         * Возвращает длину поля
         * @return int
         */
        public function length()
        {
            return $this->get_param('length', 11);
        }

        /**
         * Возвращает значение по умолчанию
         * @return mixed
         */
        public function default_value()
        {
            return $this->get_param('default', false);
        }

    }
