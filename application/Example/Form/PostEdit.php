<?php

    namespace App\Example\Form;

    use DMF\Core\Form\ModelForm;

    /**
     * Генерация схемы формы из схемы модели
     */
    class PostEdit extends ModelForm
    {

        /** @var string Имя связанной с классом модели */
        public $model = 'Example.Post';

        /**
         * Массив лейблов полей
         * @return array
         */
        public function labels()
        {
            return [
                'name'       => 'Название статьи',
                'text'       => 'Текст статьи',
                'created_at' => 'Дата публикации',
                'status'     => 'Статус статьи'
            ];
        }

        /**
         * Массив исключенных из схемы полей
         * @return array
         */
        public function excluded_fields()
        {
            return ['created_at'];
        }

    }
