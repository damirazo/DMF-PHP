<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * TextField.php
     * 21.11.12, 17:21
     */

    namespace DMF\Core\Model\Field;

    /**
     * Поле для хранения текста
     */
    class TextField extends BaseField
    {

        public function create_sql($name)
        {
            $nullable = $this->get_param('nullable', false) === true ? 'NULL' : 'NOT NULL';
            return '`'.$name.'` TEXT '.$nullable;
        }

        /**
         * Возвращает тип поля
         * @return string
         */
        public function type()
        {
            return 'text';
        }

        /**
         * Возвращает длину поля
         * @return int
         */
        public function length()
        {
            return 1;
        }

    }
