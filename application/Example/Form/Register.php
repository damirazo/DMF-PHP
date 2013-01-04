<?php

    namespace App\Example\Form;

    use DMF\Core\Form\Form;

    class Register extends Form
    {

        public $csrf = true;

        public function scheme()
        {
            return [
                'username'        => [
                    'type'  => 'string',
                    'label' => 'Имя пользователя',
                    'rules' => ['required' => true, 'min_length' => 5, 'max_length' => 16]
                ],
                'email'           => [
                    'type'  => 'email',
                    'label' => 'Адрес эл. почты',
                    'rules' => ['required' => true]
                ],
                'password'        => [
                    'type'  => 'password',
                    'label' => 'Пароль',
                    'rules' => ['required' => true, 'matches_to' => 'password_repeat']
                ],
                'password_repeat' => [
                    'type'  => 'password',
                    'label' => 'Повторите пароль'
                ]
            ];
        }

    }
