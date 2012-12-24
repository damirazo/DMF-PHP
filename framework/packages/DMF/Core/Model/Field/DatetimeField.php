<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * DatetimeField.php
     * 21.11.12, 17:21
     */

    namespace DMF\Core\Model\Field;

    /**
     * Поле для хранения даты и времени
     */
    class DatetimeField extends BaseField
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
            $default_value = $default === false ? '' : 'DEFAULT ' . $default;

            return '`' . $name . '` TIMESTAMP ' . $nullable . ' ' . $default_value;
        }

        /**
         * Возвращает тип поля
         * @return string
         */
        public function type()
        {
            return 'timestamp';
        }

        /**
         * Возвращает длину поля
         * @return int
         */
        public function length()
        {
            return 20;
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
