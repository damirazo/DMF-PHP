<?php

    namespace App\Example\Form;

    use DMF\Core\Form\Form;

    /**
     * Форма регистрации нового пользователя
     */
    class Register extends Form
    {

        /**
         * Схема формы
         */
        public function scheme()
        {
            return [
                // Имя пользователя
                'username'        => [
                    'type'  => 'DMF.InputField',
                    'label' => 'Имя пользователя',
                    'rules' => [
                        'required',
                        'min_length=5',
                        'max_length=24',
                        'check_username'
                    ],
                ],
                // Адрес эл.почты
                'email'           => [
                    'type'  => 'DMF.EmailField',
                    'label' => 'Адрес эл. почты',
                    'rules' => ['required', 'check_email']
                ],
                // Пароль
                'password'        => [
                    'type'  => 'DMF.PasswordField',
                    'label' => 'Пароль',
                    'rules' => [
                        'required',
                        'min_length=5',
                        'matches_to=password_repeat'
                    ]
                ],
                // Повтор пароля
                'password_repeat' => [
                    'type'  => 'DMF.PasswordField',
                    'label' => 'Повторите пароль'
                ],
            ];
        }

        /**
         * Проверка существования пользователя с тем же именем
         */
        public function rule__username__check_username($value, $rule)
        {
            if ($this->model('User')->check_username($value)) {
                return 'Имя пользователя "' . $value . '" уже используется на данном сайте!';
            }
            return false;
        }

        /**
         * Проверка существования пользователя с тем же адресом эл.почты
         */
        public function rule__email__check_email($value, $rule)
        {
            if ($this->model('User')->check_email($value)) {
                return 'Адрес эл.почты "' . $value . '" уже используется на данном сайте!';
            }
            return false;
        }

    }
