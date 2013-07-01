<?php

    namespace DMF\Core\Model;

    use ArrayAccess;

    /**
     * Коллекция сущностей
     */
    class EntityCollection implements \ArrayAccess, \Iterator
    {

        /** @var \DMF\Core\Model\Model|null */
        protected $model = null;
        /** @var array Коллекция сущностей */
        protected $data = [];
        /** @var null|string Имя таблицы в БД */
        protected $table = null;

        /**
         * Инициализация коллекции компонентов
         * @param \DMF\Core\Model\Model $model Базовая модель, содержащая коллекцию
         * @param array                 $data  Список данных для коллекции
         */
        public function __construct($model, $data = [])
        {
            $this->model = $model;
            $this->table = $this->model->table_name();
            if (count($data) > 0) {
                foreach ($data as $element) {
                    $entity_namespace = $this->model->entity_namespace();
                    $entity = new $entity_namespace($this->model, $element);
                    $this->add($entity);
                }
            }
        }

        /**
         * Добавление сущности в коллекцию
         * @param Entity $entity Сущность
         */
        public function add(\DMF\Core\Model\Entity $entity)
        {
            $this->data[] = $entity;
        }

        /**
         * Сохранение измененных в коллекции сущностей
         */
        public function save()
        {
            /** @var Entity $entity */
            foreach ($this->data as $entity) {
                $entity->save();
            }
        }

        /**
         * Возвращает количество сущностей в коллекции
         * @return int Количество сущностей в коллекции
         */
        public function count()
        {
            return count($this->data);
        }

        /**
         * Установка значения
         * @param mixed $offset Смещение (ключ) элемента
         * @param mixed $value  Значение элемента
         */
        public function offsetSet($offset, $value)
        {
            // запрещаем ручное добавление объектов
        }

        /**
         * Получение значения
         * @param mixed $offset Смещение (ключ) элемента
         * @return mixed|null
         */
        public function offsetGet($offset)
        {
            return isset($this->data[$offset]) ? $this->data[$offset] : null;
        }

        /**
         * Проверка существования значения
         * @param mixed $offset Смещение (ключ) элемента
         * @return bool
         */
        public function offsetExists($offset)
        {
            return !!(isset($this->data[$offset]));
        }

        /**
         * Удаление значения
         * @param mixed $offset Смещение (ключ) элемента
         */
        public function offsetUnset($offset)
        {
            unset($this->data[$offset]);
        }

        /**
         * Возвращение итератора на первый элемент
         * @return mixed|void
         */
        public function rewind()
        {
            return reset($this->data);
        }

        /**
         * Возвращает текущий элемент итератора
         * @return mixed
         */
        public function current()
        {
            return current($this->data);
        }

        /**
         * Возвращает ключ текущего элемента итератора
         * @return mixed
         */
        public function key()
        {
            return key($this->data);
        }

        /**
         * Перевод итератора на следующий элемент
         * @return mixed|void
         */
        public function next()
        {
            return next($this->data);
        }

        /**
         * Проверка существования элемента
         * @return bool
         */
        public function valid()
        {
            return key($this->data) !== null;
        }

        /**
         * Вернуть элемент коллекции по его индексу
         * @param int $index Индекс элемента
         * @return null|\DMF\Core\Model\Entity
         */
        public function index($index)
        {
            if (isset($this->data[$index])) {
                return $this->data[$index];
            }
            return null;
        }

    }
