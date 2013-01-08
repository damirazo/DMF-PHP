<?php

    namespace App\Example\Form;

    use DMF\Core\Form\ModelForm;

    class PostEdit extends ModelForm
    {

        public $model = 'Example.Post';

        public function labels()
        {
            return [
                'name' => 'Название статьи',
                'created_at' => 'Дата публикации'
            ];
        }

    }
