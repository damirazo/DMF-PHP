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

        /** {@inheritdoc} */
        public function length()
        {
            return 1;
        }

        /** {@inheritdoc} */
        public function type()
        {
            return 'text';
        }

        /** {@inheritdoc} */
        public function sql_type()
        {
            return 'TEXT';
        }

    }
