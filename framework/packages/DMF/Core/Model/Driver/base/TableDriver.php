<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Driver\base;

    /**
     * Class TableDriver
     * Базовый класс для драйвера таблиц
     *
     * @package DMF\Core\Model\Driver\base
     */
    abstract class TableDriver
    {

        /** @var  array Схема таблиц в БД */
        protected $scheme;

        /**
         * Инициализация драйвера
         * @param array $table_scheme Схема таблиц в БД
         */
        public function __construct($table_scheme)
        {
            $this->scheme = $table_scheme;
        }

        /**
         * Генерация SQL запроса для создания таблицы в БД
         */
        public function sql_for_table()
        {

        }

        public function sql_for_fields()
        {
            $namespace = __NAMESPACE__;
            $result = [];
            /** @var \DMF\Core\Model\Field\BaseField $field */
            foreach ($this->scheme as $field) {
                $field_driver_cls = $namespace . '\\Field\\' . $field->class_name();
                /** @var \DMF\Core\Model\Driver\base\FieldDriver $field_driver */
                $field_driver = new $field_driver_cls($field);
                $result[] = $field_driver->get_sql();
            }
            return $result;
        }

    }