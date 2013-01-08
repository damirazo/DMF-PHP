<?php

    namespace DMF\Core\Form\Field;

    /**
     * Поле для ввода пароля
     */
    class PasswordField extends InputField
    {

        /** {@inheritdoc} */
        protected $type = 'password';

        /** Проверка значения поля на совпадение со значением другого поля */
        protected function rule__matches_to($value, $rule)
        {
            /** @var $matched_field \DMF\Core\Form\Field\BaseField */
            $matched_field = $this->form->field($rule);
            if ($value != $matched_field->value()) {
                return sprintf(
                    $this->error_message(
                        'required',
                        'Значение поля "%s" должно совпадать со значением поля "%s"!'
                    ),
                    $this->label(), $matched_field->label()
                );
            }
            return false;
        }

    }
