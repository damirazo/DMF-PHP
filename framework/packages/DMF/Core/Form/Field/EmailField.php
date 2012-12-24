<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * EmailField.php
     * 28.11.12, 0:16
     */

    namespace DMF\Core\Form\Field;

    /**
     * Поле для электропочты
     */
    class EmailField extends InputField
    {

        /**
         * {@inheritdoc}
         */
        public function validate($form, $value, $label)
        {
            /** @var $validator \DMF\Core\Form\Validator */
            $validator = parent::validate($form, $value, $label);
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $validator->add_error('Значение поля "'.$label.'" содержит некорректный E-Mail адрес!');
            }
            return $validator;
        }

    }
