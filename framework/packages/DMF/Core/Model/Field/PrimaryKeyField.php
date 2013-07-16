<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Field;

    /**
     * Class PrimaryKeyField
     * Поле для хранения первичного ключа
     *
     * @package DMF\Core\Model\Field
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
