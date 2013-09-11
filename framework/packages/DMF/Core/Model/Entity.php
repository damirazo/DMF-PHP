<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model;

    use ArrayAccess;
    use DMF\Core\Component\Component;
    use DMF\Core\Model\Exception\RecordDoesNotExists;

    /**
     * Class Entity
     * Описание сущности, хранимой в БД
     *
     * @package DMF\Core\Model
     */
    class Entity extends Component implements \ArrayAccess, \Iterator
    {

        /** @var bool Внесены ли изменения в сущность */
        public $is_modified = false;

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

        /** @var array Кэш объектов связанных моделей */
        protected $relation_cache = [];

        /**
         * Конструктор сущности
         * @param Model $model Связанная модель
         * @param array $data  Массив данных сущности
         */
        public function __construct(Model $model, array $data = [])
        {
            $this->model = $model;
            $this->table = $model->table_name();
            $this->data = $data;
            $this->pk_name = $model->primary_key();
            $this->pk = $data[$model->primary_key()];
        }

        /**
         * Сохранение сущности в БД
         */
        public function save()
        {
            // Если сущность не была изменена, то игнорируем ее сохранение
            if ($this->is_modified) {
                $this->model->update_by_pk($this->pk, $this->data);
            }
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

        /**
         * Возвращает поле с требуемым именем у данной сущности
         * @param string $name Название поля
         * @return bool|\DMF\Core\Model\Field\BaseField
         */
        public function get_field_by_name($name)
        {
            $scheme = $this->model->scheme();
            if (isset($scheme[$name])) {
                return $scheme[$name];
            }
            return false;
        }

        /**
         * Получение свойства с требуемым именем
         * @param string $name Имя свойства
         * @return null|\DMF\Core\Model\Entity
         */
        public function __get($name)
        {
            /** @var $field \DMF\Core\Model\Field\ForeignkeyField */
            $field = $this->get_field_by_name($name);

            $value = null;
            if (isset($this->data[$name])) {
                $value = $this->data[$name];
            }

            // Если указана связь на другую таблицу,
            // то извлекаем соответствующую запись
            if ($field instanceof \DMF\Core\Model\Field\ForeignKeyField) {
                if (isset($this->relation_cache[$name])) {
                    return $this->relation_cache[$name];
                } else {
                    /** @var $related_model \DMF\Core\Model\Model */
                    $related_model = $this->model($field->chained_field);
                    $record = $related_model->get_by_pk((int)$value);
                    $this->relation_cache[$name] = $record;
                    return $record;
                }
            }

            return $value;
        }

        /**
         * Установка свойства с требуемым именем
         * @param string                     $name  Имя свойства
         * @param int|\DMF\Core\Model\Entity $value Значение свойства
         * @throws RecordDoesNotExists
         */
        public function __set($name, $value)
        {
            /** @var $field \DMF\Core\Model\Field\ForeignkeyField */
            $field = $this->get_field_by_name($name);

            // Для полей со связью требуется проверить существование связанного объекта
            if ($field instanceof \DMF\Core\Model\Field\ForeignKeyField) {
                $relation = $this->model($field->chained_field);
                $pk = is_int($value) ? $value : $value->pk;
                try {
                    $relation->get_by_pk($pk);
                } catch (RecordDoesNotExists $exception) {
                    // Если связанный объект не найден, то выбрасываем исключение
                    throw $exception;
                }
            }

            if (isset($this->data[$name])) {
                $this->data[$name] = $value;
                $this->is_modified = true;
            }
        }

    }
