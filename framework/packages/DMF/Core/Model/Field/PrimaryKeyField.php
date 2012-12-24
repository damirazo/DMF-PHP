<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * PrimaryKeyField.php
     * 21.11.12, 17:17
     */

    namespace DMF\Core\Model\Field;

    /**
     * Поле первичного ключа
     */
    class PrimaryKeyField extends BaseField
    {

        public function create_sql($name)
        {
            $autoincrement = ($this->get_param('auto_increment', true) === true) ? 'AUTO_INCREMENT' : '';

            return '`' . $name . '` INT(11) NOT NULL ' . $autoincrement . ' PRIMARY KEY';
        }

        /**
         * Возвращает тип поля
         * @return string
         */
        public function type()
        {
            return 'primary_key';
        }

        /**
         * Возвращает длину поля
         * @return int
         */
        public function length()
        {
            return 11;
        }

    }
