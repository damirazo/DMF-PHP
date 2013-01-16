<?php

    namespace App\Example\Form;

    use DMF\Core\Form\Form;

    class Login extends Form
    {

        public function scheme()
        {
            return [
                'username' => [
                    'type' => 'DMF.InputField',
                    'label' => 'Имя пользователя'
                ]
            ];
        }

    }
