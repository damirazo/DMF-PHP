<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * InputField.php
     * 27.11.12, 17:36
     */

    namespace DMF\Core\Form\Field;

    /**
     * Класс реализующий стандартное поле формы
     */
    class InputField extends BaseField
    {

        /**
         * {@inheritdoc}
         */
        public function _get_html_code($name, $default)
        {
            $value = 'value="' . $default . '"';

            return '<input name="' . $name . '" ' . $this->_params() . ' ' . $value . ' >';
        }

        /**
         * {@inheritdoc}
         */
        public function _defaults()
        {
            return [
                'type' => 'text'
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function validate($form, $value, $label)
        {
            $validator = parent::validate($form, $value, $label);
            return $validator;
        }

    }
