<?php

    namespace App\Example\Model;

    use DMF\Core\Model\Model;
    use DMF\Core\Model\Field\BooleanField;
    use DMF\Core\Model\Field\DatetimeField;
    use DMF\Core\Model\Field\TextField;
    use DMF\Core\Model\Field\CharField;
    use DMF\Core\Model\Field\PrimaryKeyField;

    /**
     * Базовый пример модели
     */
    class Post extends Model
    {

        /** Схема модели */
        public function _scheme()
        {
            return [
                'id'         => new PrimaryKeyField(),
                'name'       => new CharField(['length' => 64, 'default' => 'Название']),
                'text'       => new TextField(),
                'created_at' => new DatetimeField(),
                'status'     => new BooleanField(['default' => true])
            ];
        }

    }
