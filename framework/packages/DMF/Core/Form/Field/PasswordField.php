<?php

    namespace DMF\Core\Form\Field;

    /**
     * Поле для ввода пароля
     */
    class PasswordField extends InputField
    {

        /** {@inheritdoc} */
        protected $type = 'password';

        /**
         * Проверка значения поля на совпадение со значением другого поля
         * @param string $value Значение поля
         * @param string $rule  Имя поля, с которым производится сравнение
         */
        protected function rule__matches_to($value, $rule)
        {
            /** @var $matched_field \DMF\Core\Form\Field\BaseField */
            $matched_field = $this->form->field($rule);
            if ($value != $matched_field->value()) {
                $this->validator->add_error(
                    'Значение поля "' . $this->label() . '" должно совпадать со значением поля "'
                            . $matched_field->label() . '"!'
                );
            }
        }

    }
