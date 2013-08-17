<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace App\Example\Model;

    use DMF\Core\Model\Field\CharField;
    use DMF\Core\Model\Field\ForeignKeyField;
    use DMF\Core\Model\Field\PrimaryKeyField;
    use DMF\Core\Model\Model;

    class First extends Model
    {

        public function scheme()
        {
            return [
                'id' => new PrimaryKeyField(),
                'name' => new CharField(['length' => 128]),
                'second_data' => new ForeignKeyField('Example.Second'),
            ];
        }

    }