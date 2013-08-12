<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Field;

    /**
     * Class IntegerField
     * Поле для хранения числового значения
     *
     * @package DMF\Core\Model\Field
     */
    class IntegerField extends BaseField
    {

        /** {@inheritdoc} */
        public function length()
        {
            return $this->get_param('length', 11);
        }

        /** {@inheritdoc} */
        public function type()
        {
            return 'integer';
        }

        /** {@inheritdoc} */
        public function sql_type()
        {
            return 'INT(' . $this->length() . ')';
        }

    }
