<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Field;

    /**
     * Class BooleanField
     * Поле для хранения логического значения
     *
     * @package DMF\Core\Model\Field
     */
    class BooleanField extends BaseField
    {

        /** {@inheritdoc} */
        public function length()
        {
            return 1;
        }

        /** {@inheritdoc} */
        public function default_value()
        {
            return $this->get_param('default') ? 1 : 0;
        }

        /** {@inheritdoc} */
        public function type()
        {
            return 'boolean';
        }

        /** {@inheritdoc} */
        public function sql_type()
        {
            return 'TINYINT(1)';
        }

    }
