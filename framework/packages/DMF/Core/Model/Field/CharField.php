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

        /** {@inheritdoc} */
        public function length()
        {
            return $this->get_param('length', 255);
        }

        /** {@inheritdoc} */
        public function type()
        {
            return 'string';
        }

        /** {@inheritdoc} */
        public function sql_type()
        {
            return 'VARCHAR(' . $this->length() . ')';
        }

    }
