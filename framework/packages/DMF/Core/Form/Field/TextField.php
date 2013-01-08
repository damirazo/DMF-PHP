<?php

    namespace DMF\Core\Form\Field;

    /**
     * Текстовое поле
     */
    class TextField extends BaseField
    {

        /** {@inheritdoc} */
        public function html()
        {
            return '<textarea name="' . $this->name . '" ' . $this->html_attrs()
                    . '>' . $this->value() . '</textarea>';
        }

    }
