<?php

    namespace DMF\Core\Model;

    use ArrayAccess;

    /**
     * Базовая модуль для сущности из БД
     */
    class Entity implements \ArrayAccess
    {

        /** @var array Массив данных сущности */
        protected $data = [];

        /** @var null|string Имя таблицы */
        protected $table = null;

        /** @var bool Внесены ли изменения в сущность */
        public $is_modified = false;

        /**
         * Конструктор сущности
         * @param array  $data  Массив значений
         * @param string $table Имя таблицы в БД
         */
        public function __construct($table, array $data = [])
        {
            $this->table = $table;
            $this->data = $data;
        }

        /**
         * Установка значения
         * @param mixed $offset Смещение (ключ) элемента
         * @param mixed $value  Значение элемента
         */
        public function offsetSet($offset, $value)
        {
            if (isset($this->data[$offset])) {
                $this->data[$offset] = $value;
                $this->is_modified = true;
            }
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
            $this->is_modified = true;
        }

    }
