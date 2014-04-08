<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */    
        
    namespace ORM\Field;
    
        
    class CharField extends Field
    {

        public function to_sql()
        {
            $is_null = $this->param('is_null') ? 'NULL' : 'NOT NULL';
            return sprintf('`%s` VARCHAR(%d) %s', $this->get_name(), $this->param('length'), $is_null);
        }

        protected function params()
        {
            return array_merge([
                'length' => 128,
                'is_null' => false,
            ], $this->params);
        }

    } 