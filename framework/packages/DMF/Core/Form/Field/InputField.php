<?php

    namespace DMF\Core\Form\Field;

    /**
     * Простое текстовое поле
     */
    class InputField extends BaseField
    {

        /** @var string Тип поля */
        protected $type = 'text';

        /** {@inheritdoc} */
        public function html()
        {
            $value = ($this->value()) ? $this->value() : '';
            return '<input type="' . $this->type . '" name="' . $this->name . '" value="' . $value . '">';
        }

    }
