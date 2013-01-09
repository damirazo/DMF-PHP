<?php

    namespace DMF\Core\Model;

    use ArrayAccess;
    use DMF\Core\Component\Component;

    /**
     * Базовая модуль для сущности из БД
     */
    class Entity extends Component implements \ArrayAccess, \Iterator
    {

        /** @var null|Model Связанная модель */
        protected $model = null;

        /** @var array Массив данных сущности */
        protected $data = [];

        /** @var null|string Имя таблицы */
        protected $table = null;

        /** @var null|int ID сущности в БД */
        protected $pk = null;

        /** @var null|string Имя поля первичного ключа */
        protected $pk_name = null;

        /** @var bool Внесены ли изменения в сущность */
        public $is_modified = false;

        /**
         * Конструктор сущности
         * @param Model $model Связанная модель
         * @param array $data  Массив данных сущности
         */
        public function __construct(Model $model, array $data = [])
        {
            $this->model = $model;
            $this->table = $model->_get_table_name();
            $this->data = $data;
            $this->pk_name = $model->_get_primary_key_field_name();
            $this->pk = $data[$model->_get_primary_key_field_name()];
        }

        /**
         * Сохранение сущности в БД
         */
        public function save()
        {
            // Если сущность не была изменена, то игнорируем ее сохранение
            if ($this->is_modified) {
                self::$db->query(
                    'UPDATE ' . $this->table . ' SET '
                            . $this->get_update_condition() . ' WHERE ' . $this->pk_name . '=' . $this->pk,
                    $this->data
                )->send();
            }
        }

        /**
         * Возвращает строку с сохраняемыми значениями
         * @return string
         */
        protected function get_update_condition()
        {
            $condition = [];
            foreach ($this->data as $key => $value) {
                $condition[] = $key . '=:' . $key;
            }
            return implode(', ', $condition);
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

    }
