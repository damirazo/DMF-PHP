<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace App\Example\Model;

    use DMF\Core\Model\Field\CharField;
    use DMF\Core\Model\Field\IntegerField;
    use DMF\Core\Model\Field\PrimaryKeyField;
    use DMF\Core\Model\Model;

    class Second extends Model
    {

        public function scheme()
        {
            return [
                'id' => new PrimaryKeyField(),
                'code' => new IntegerField(['default' => 100500]),
                'name' => new CharField(),
            ];
        }

    }