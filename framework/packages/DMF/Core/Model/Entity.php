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

        /**
         * Конструктор сущности
         * @param array $data Массив значений
         */
        public function __construct(array $data = [])
        {
            $this->data = $data;
        }

        /**
         * Установка значения
         * @param mixed $offset Смещение (ключ) элемента
         * @param mixed $value  Значение элемента
         */
        public function offsetSet($offset, $value)
        {
            $this->data[$offset] = $value;
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
