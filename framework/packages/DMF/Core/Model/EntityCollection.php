<?php

    namespace DMF\Core\Model;

    use ArrayAccess;

    /**
     * Коллекция сущностей
     */
    class EntityCollection implements \ArrayAccess
    {

        /** @var array Коллекция сущностей */
        protected $data = [];

        /** @var null|string Имя таблицы в БД */
        protected $table = null;

        /** Конструктор */
        public function __construct($table)
        {
            $this->table = $table;
        }

        /**
         * Добавление сущности в коллекцию
         * @param Entity $entity Сущность
         */
        public function add_entity(\DMF\Core\Model\Entity $entity)
        {
            $this->data[] = $entity;
        }

        /**
         * Сохранение измененных в коллекции сущностей
         */
        public function save()
        {
            /** @var Entity $entity */
            foreach($this->data as $entity) {
                $entity->save();
            }
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

    }
