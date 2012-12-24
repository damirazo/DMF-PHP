<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * BooleanField.php
     * 21.11.12, 17:21
     */

    namespace DMF\Core\Model\Field;

    /**
     * Поле для хранения булева значения
     */
    class BooleanField extends BaseField
    {

        /**
         * Возвращает строку, содержащую код для создания данного поля
         * @param string $name Имя поля
         *
         * @return string
         *
         * Используемые параметры и их значения по умолчанию:
         * default: true
         */
        public function create_sql($name)
        {
            $default = 'DEFAULT ' . (($this->get_param('default', true) === true) ? '1' : '0');

            return '`' . $name . '` TINYINT(1) ' . $default;
        }

        /**
         * Возвращает тип поля
         * @return string
         */
        public function type()
        {
            return 'boolean';
        }

        /**
         * Возвращает длину поля
         * @return int
         */
        public function length()
        {
            return 1;
        }

        /**
         * Возвращает значение по умолчанию
         * @return mixed
         */
        public function default_value()
        {
            return $this->get_param('default', true);
        }

    }
