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

        /** {@inheritdoc} */
        public function sql($name)
        {
            $sql = parent::sql($name);
            $autoincrement = $this->get_param('auto_increment', true) ? 'AUTO_INCREMENT' : '';
            return $sql . $autoincrement . ' PRIMARY KEY';
        }

        /** {@inheritdoc} */
        public function length()
        {
            return 11;
        }

        /** {@inheritdoc} */
        public function type()
        {
            return 'primary_key';
        }

        /** {@inheritdoc} */
        public function sql_type()
        {
            return 'INT(11)';
        }

    }
