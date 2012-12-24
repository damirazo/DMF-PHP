<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * TextField.php
     * 27.11.12, 17:53
     */

    namespace DMF\Core\Form\Field;

    /**
     * Класс реализует текстовое поле
     */
    class TextField extends BaseField
    {

        /**
         * {@inheritdoc}
         */
        public function _get_html_code($name, $default)
        {
            $value = $default;
            return '<textarea name="' . $name . '" ' . $this->_params() . ' >'.$value.'</textarea>';
        }

        /**
         * {@inheritdoc}
         */
        public function _defaults()
        {
            return [
                'cols'      => 50,
                'rows'      => 5,
                'maxlength' => 1024
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
