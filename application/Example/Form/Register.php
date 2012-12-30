<?php

    namespace App\Example\Form;

    use DMF\Core\Form\Form;
    use DMF\Core\Form\Field\EmailField;
    use DMF\Core\Form\Field\PasswordField;
    use DMF\Core\Form\Field\InputField;

    class Register extends Form
    {

        public $csrf_protection = true;

        public function scheme()
        {
            return [
                'username'        => new InputField('Имя пользователя', ['min_length' => 5, 'max_length' => 16]),
                'email'           => new EmailField('Электропочта'),
                'password'        => new PasswordField('Пароль', ['min_length' => 5]),
                'password_repeat' => new PasswordField('Повторите пароль', ['matches_to' => 'password'])
            ];
        }

    }
