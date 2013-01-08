<?php

    namespace App\Example\Form;

    use DMF\Core\Form\Form;

    class Register extends Form
    {

        public function scheme()
        {
            return [
                'username'        => [
                    'type'   => 'DMF.InputField',
                    'label'  => 'Имя пользователя',
                    'rules'  => [],
                    'attrs'  => ['class' => 'form-field']
                ],
                'email'           => [
                    'type'  => 'DMF.EmailField',
                    'label' => 'Адрес эл. почты'
                ],
                'password'        => [
                    'type'  => 'DMF.PasswordField',
                    'label' => 'Пароль',
                    'rules' => []
                ],
                'password_repeat' => [
                    'type'  => 'DMF.PasswordField',
                    'label' => 'Повторите пароль'
                ],
                'description'     => [
                    'type'  => 'DMF.TextField',
                    'label' => 'Описание'
                ],
                'city'            => [
                    'type'    => 'DMF.RadioField',
                    'label'   => 'Город',
                    'options' => [
                        'kazan'  => 'Казань',
                        'moscow' => 'Москва'
                    ],
                    'rules' => []
                ],
                'gender' => [
                    'type' => 'DMF.CheckboxField',
                    'label' => 'Укажите пол',
                    'options' => [
                        'm' => 'Мужской',
                        'f' => 'Женский'
                    ],
                    'rules' => []
                ],
                'list' => [
                    'type' => 'DMF.SelectField',
                    'label' => 'Список',
                    'options' => [
                        'p1' => 'Первый элемент',
                        'p2' => 'Второй элемент',
                        'p3' => 'Третий элемент'
                    ]
                ],
                'multilist' => [
                    'type' => 'DMF.MultiSelectField',
                    'label' => 'Мульти-список',
                    'options' => [
                        'p1' => 'Первый элемент',
                        'p2' => 'Второй элемент',
                        'p3' => 'Третий элемент'
                    ],
                    'rules' => [],
                    'errors' => ['required' => 'Уупс, пропущено обязательное поле "%s"!']
                ],
                'datetime' => [
                    'type' => 'DMF.DatetimeField',
                    'label' => 'Дата и время'
                ]
            ];
        }

    }
