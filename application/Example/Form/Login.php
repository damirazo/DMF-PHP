<?php

    namespace App\Example\Form;

    use DMF\Core\Form\Form;

    /**
     * Форма входа пользователя на сайт
     */
    class Login extends Form
    {

        public function scheme()
        {
            return [
                'username' => [
                    'type' => 'DMF.InputField',
                    'label' => 'Имя пользователя',
                    'rules' => ['required', 'check_user']
                ],
                'password' => [
                    'type' => 'DMF.PasswordField',
                    'label' => 'Пароль',
                    'rules' => ['required']
                ]
            ];
        }

        /** Проверка существования пользователя с указанными данными */
        public function rule__username__check_user($value, $rule)
        {
            /** @var $password_field \DMF\Core\Form\Field\BaseField */
            $password_field = $this->field('password');
            if (!$this->model('User')->is_exists($value, $password_field->value())) {
                return 'Пользователя с указанным именем и паролем не обнаружено!';
            }
            return false;
        }

    }
