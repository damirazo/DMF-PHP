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
    use DMF\Core\Model\Exception\RequiredFieldNotExists;

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
        /** @var array Список полей сущности */
        public $fields = [];
        /** @var null|string Имя поля первичного ключа */
        public $pk_name = null;
        /** @var null|int ID сущности в БД */
        protected $pk = null;
        /** @var null|Model Связанная модель */
        protected $model = null;
        /** @var array Массив данных сущности */
        protected $data = [];
        /** @var null|string Имя таблицы */
        protected $table = null;
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
            $this->fields = $model->scheme();
            $this->table = $model->table_name();
            $this->data = $data;
            $this->pk_name = $model->primary_key();
            // Для еще не созданных записей значение первичного ключа всегда будет null
            if (isset($this->data[$this->pk_name])) {
                $this->pk = $this->data[$this->pk_name];
            } else {
                $this->pk = null;
                // Если запись еще не сохранена, то указываем, что она была изменена
                $this->is_modified = true;
            }
        }

        /**
         * Сохранение сущности в БД
         */
        public function save()
        {
            // Если сущность не была изменена, то игнорируем ее сохранение
            if ($this->is_modified) {
                $model = $this->model;
                // Валидация значений полей
                $this->validate_all();
                // Если сущность уже была сохранена, то обновляем запись в БД
                // В противном случае сохраняем запись и получаем ее id
                if (!is_null($this->pk)) {
                    $model->update_by_pk($this->pk, $this->data);
                } else {
                    $this->id = $model->create($this->data);
                }
            }
            // После обновления записи ставим отметку, что запись не изменена, чтобы избежать повторных пересохранений
            $this->is_modified = false;
        }

        /**
         * Валидация всех полей сущности
         */
        public function validate_all()
        {
            $fields = $this->fields;
            /** @var $field \DMF\Core\Model\Field\BaseField */
            foreach ($fields as $field_name => $field) {
                $this->validate($field_name, $field);
            }
        }

        /**
         * Валидация значения поля
         * @param string                          $name Имя поля
         * @param \DMF\Core\Model\Field\BaseField $field Объект поля
         * @throws \DMF\Core\Model\Exception\RequiredFieldNotExists
         */
        protected function validate($name, $field)
        {
            // Проверка, что присутствует значение для полей, обязательных к заполнению
            if (!isset($this->data[$name]) && $field->is_required()) {
                throw new RequiredFieldNotExists(
                    sprintf(
                        'Поле %s.%s обязательно к заполнению, но не содержит значения,'
                        . ' либо не указано значение по умолчанию!',
                        $this->model->class_name(),
                        $name
                    )
                );
            }
        }

        /**
         * Проверка существования у сущности поля с указанным значением
         * @param string $name Имя поля
         * @return bool
         */
        public function has_field($name)
        {
            return isset($this->fields[$name]);
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
            if (isset($this->fields[$name])) {
                return $this->fields[$name];
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

            // При установке значения id обновляем также значение pk
            if ($name == $this->pk_name || $name == 'pk') {
                $this->pk = $value;
                $this->data[$this->pk_name] = $value;
            }

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
