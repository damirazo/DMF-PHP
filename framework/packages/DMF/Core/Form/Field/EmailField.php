<?php

    namespace DMF\Core\Form\Field;

    /**
     * Поле электронной почты
     */
    class EmailField extends InputField
    {

        /** {@inheritdoc} */
        protected $rules = ['validate_email'];

        /** {@inheritdoc} */
        protected $type = 'text';

        /** Проверка адреса эл.почты на корректность */
        protected function rule__validate_email($value, $rule)
        {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL) && $value != '') {
                return sprintf(
                    $this->error_message(
                        'validate_email',
                        'Значение поле "%s" содержит неверный адрес эл.почты!'
                    ),
                    $this->label()
                );
            }
            return false;
        }

    }
