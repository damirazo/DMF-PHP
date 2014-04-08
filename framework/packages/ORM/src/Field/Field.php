<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */
        
    namespace ORM\Field;

    use ORM\Exception\FieldParamDoesNotExists;

    /**
     * Class Field
     * @package DMF_ORM\Field
     *
     * Базовый класс для описания поля в БД
     */
    abstract class Field
    {

        /** @var array Список параметров */
        protected $params = [];
        /** @var \ORM\Model\Model|null Модель, в которой объявлено поле */
        protected $model = null;
        /** @var string|null Имя данного поля */
        protected $name = null;

        /**
         * Конструктор поля
         * @param array $params Список параметров
         */
        public function __construct(array $params=[])
        {
            $this->params = $params;
        }

        /**
         * Инициализация поля с помощью модели
         * @param \ORM\Model\Model $model Модель, которой принадлежит данное поле
         * @param string $field_name Имя поля
         */
        public function init($model, $field_name)
        {
            $this->model = $model;
            $this->name = $field_name;
        }

        /**
         * Возвращает параметр данного поля с указанным именем
         * @param string $param_name Имя параметра
         * @return mixed
         * @throws \ORM\Exception\FieldParamDoesNotExists
         */
        public function param($param_name)
        {
            if (array_key_exists($param_name, $this->params())) {
                return $this->params()[$param_name];
            }
            throw new FieldParamDoesNotExists(
                sprintf('Значение %s отсутствует у поля %s', $param_name, $this->get_name())
            );
        }

        /**
         * Возвращает экземпляр модели, которой принадлежит данное поле
         * @return \ORM\Model\Model|null
         */
        public function get_model()
        {
            return $this->model;
        }

        /**
         * Возвращает имя данного поля
         * @return null|string
         */
        public function get_name()
        {
            return $this->name;
        }

        /**
         * Метод, возвращающий описание SQL для данного поля
         * Если возвращается список, то второе значение списка
         * используется для дополнительной настройки таблицы
         * @example varchar(128)
         * @return mixed
         */
        abstract public function to_sql();

        /**
         * Возвращает список параметров данного поля
         * В случае отсутствия какого-либо параметра используются значения по умолчанию
         * @return mixed
         */
        abstract protected function params();

    } 