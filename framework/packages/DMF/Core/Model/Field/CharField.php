<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Field;

    /**
     * Class CharField
     * Поле для хранения строкового значения
     *
     * @package DMF\Core\Model\Field
     */
    class CharField extends BaseField
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
         * length: 255
         */
        public function create_sql($name)
        {
            $nullable = ($this->get_param('nullable', false) === true) ? 'NULL' : 'NOT NULL';
            $default = $this->get_param('default', false);
            $default_value = $default === false ? '' : 'DEFAULT "'.$default.'"';

            return '`' . $name . '` VARCHAR(' . $this->get_param('length', 255) . ') ' . $nullable . ' '
                . $default_value;
        }

        /**
         * Возвращает тип поля
         * @return string
         */
        public function type()
        {
            return 'string';
        }

        /**
         * Возвращает длину поля
         * @return int
         */
        public function length()
        {
            return $this->get_param('length', 255);
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
