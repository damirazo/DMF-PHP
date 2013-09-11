<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form\Field;

    /**
     * Class EmailField
     * Поле значения электронной почты
     *
     * @package DMF\Core\Form\Field
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
