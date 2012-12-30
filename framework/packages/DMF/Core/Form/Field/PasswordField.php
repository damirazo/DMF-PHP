<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * PasswordField.php
     * 28.11.12, 16:29
     */

    namespace DMF\Core\Form\Field;

    use DMF\Core\Http\Request;

    /**
     * Поле для ввода пароля
     */
    class PasswordField extends InputField
    {

        /**
         * {@inheritdoc}
         */
        public function _defaults()
        {
            return [
                'type' => 'password'
            ];
        }

        /**
         * {@inheritdoc}
         * @param \DMF\Core\Form\Form $form
         */
        public function validate($form, $value, $label)
        {
            /** @var $validator \DMF\Core\Form\Validator */
            $validator = parent::validate($form, $value, $label);
            if ($this->_rules('matches_to')) {
                $matches_field = $this->_rules('matches_to');
                if ($this->request()->_request($matches_field)) {
                    $matches_value = $this->request()->REQUEST($matches_field);
                    if ($matches_value != $value) {
                        $validator->add_error('Значение поля "' . $label . '" не совпадает с полем "'
                                . $form->field($matches_field)['label'] . '"!');
                    }
                }
            }

            return $validator;
        }

    }
