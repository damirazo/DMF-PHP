<?php

    namespace DMF\Core\Form\Field;

    /**
     * Поле электронной почты
     */
    class EmailField extends InputField
    {

        /** {@inheritdoc} */
        protected $rules = [
            'validate_email' => true
        ];

        /** {@inheritdoc} */
        protected $type = 'email';

        /**
         * Валидация корректности адреса эл.почты
         * @param string $value Значение
         * @param bool $rule Параметр
         */
        protected function rule__validate_email($value, $rule)
        {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->validator->add_error('Значение поле "' . $this->label() . '" содержит неверный адрес эл.почты!');
            }
        }

    }
