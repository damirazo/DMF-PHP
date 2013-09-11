<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Field;

    /**
     * Class DatetimeField
     * Поле для хранения даты и времени
     *
     * @package DMF\Core\Model\Field
     */
    class DatetimeField extends BaseField
    {

        /** @var string Значение для поля по умолчанию */
        public static $default_value = 'CURRENT_TIMESTAMP';

        /** {@inheritdoc} */
        public function length()
        {
            return 20;
        }

        /** {@inheritdoc} */
        public function default_value()
        {
            return $this->get_param('default', self::$default_value);
        }

        /** {@inheritdoc} */
        public function type()
        {
            return 'datetime';
        }

        /** {@inheritdoc} */
        public function sql_type()
        {
            return 'TIMESTAMP';
        }

    }
