<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Field;

    /**
     * Class ForeignKeyField
     * Поле для хранения связи с внешней таблицей
     *
     * @package DMF\Core\Model\Field
     */
    class ForeignKeyField extends BaseField
    {

        /** @var null|string Имя связанной модели */
        public $chained_module = null;

        /** @var null|string Имя связанного поля */
        public $chained_model = null;

        /**
         * Переопределение родительского контроллера
         * @param string $chained_field Строка с названием связанной модели и ее поля
         * @param array  $params        Массив параметров
         */
        public function __construct($chained_field, $params = [])
        {
            parent::__construct($params);
            $data = explode('.', $chained_field);
            $this->chained_module = (count($data) > 1) ? $data[1] : $this->loaded_module()->name;
            $this->chained_model = $data[0];
        }

        /**
         * {@inheritdoc}
         */
        public function create_sql($name)
        {
            $nullable = ($this->get_param('nullable', false) === true) ? 'NULL' : 'NOT NULL';
            $default = $this->get_param('default', false);
            $default_value = $default === false ? '' : 'DEFAULT ' . $default;

            return '`' . $name . '` INT(11) ' . $nullable . ' ' . $default_value;
        }

        /**
         * {@inheritdoc}
         */
        public function type()
        {
            return 'foreign_key';
        }

        /**
         * {@inheritdoc}
         */
        public function length()
        {
            return 11;
        }

    }
