<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Field;

    /**
     * Class TextField
     * Поле для хранения многострочного текстового значения
     *
     * @package DMF\Core\Model\Field
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
