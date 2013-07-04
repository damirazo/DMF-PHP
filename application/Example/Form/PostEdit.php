<?php

    namespace App\Example\Form;

    use DMF\Core\Form\ModelForm;

    /**
     * Генерация схемы формы из схемы модели
     */
    class PostEdit extends ModelForm
    {

        /** @var string Имя связанной с классом модели */
        public $model = 'Post';
        /** {@inheritdoc} */
        public $rules = [
            'name' => [
                'min_length=3',
                'max_length=32'
            ],
            'text' => ['min_length=5']
        ];

        /**
         * Массив лейблов полей
         * @return array
         */
        public function labels()
        {
            return [
                'name'   => 'Название статьи',
                'text'   => 'Текст статьи',
                'status' => 'Статус статьи'
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
