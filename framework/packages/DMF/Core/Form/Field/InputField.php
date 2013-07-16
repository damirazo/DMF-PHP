<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form\Field;

    /**
     * Class InputField
     * Строковое поле
     *
     * @package DMF\Core\Form\Field
     */
    class InputField extends BaseField
    {

        /** @var string Тип поля */
        protected $type = 'text';

        /** {@inheritdoc} */
        public function html()
        {
            return '<input type="' . $this->type . '" name="' . $this->name . '" value="' . $this->value() . '" '
                    . $this->html_attrs() . '>';
        }

    }
