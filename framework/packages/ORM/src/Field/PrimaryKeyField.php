<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */    
        
    namespace ORM\Field;

    /**
     * Class PrimaryKeyField
     * @package DMF_ORM\Field
     *
     * Поле первичного ключа
     */
    class PrimaryKeyField extends Field
    {

        public function to_sql()
        {
            return sprintf('`%s` int(%d) NOT NULL AUTO_INCREMENT', $this->get_name(), $this->param('length'));
        }

        protected function params()
        {
            return array_merge([
                'length' => 11,
            ], $this->params);
        }

    }