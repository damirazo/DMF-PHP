<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */    
        
    namespace ORM\Model;

    use ORM\Field\PrimaryKeyField;

    /**
     * Class Model
     * @package DMF_ORM\Model
     *
     * Базовый класс для реализации моделей
     */
    class Model
    {

        /** @var null|string Имя таблицы в БД */
        public $table_name = null;
        /** @var string Список первичных ключей */
        public $primary_key = 'id';
        /** @var array Список уникальных ключей */
        public $unique_keys = [];
        /** @var null|array Кэш списка проинициализированных полей  */
        protected $fields = null;

        /**
         * Метод, возвращающий описание схемы БД
         * Данный метод используется только для описания структуры,
         * если требуется получить список полей - используйте метод fields!
         * @return array
         */
        public function scheme()
        {
            return [];
        }

        /**
         * Список проинициализированных полей
         * @return array
         */
        public function fields()
        {
            if (is_null($this->fields)) {
                $fields = [];
                $scheme = $this->scheme();
                // Автоматическое добавление поля с первичным ключем
                $scheme[$this->primary_key] = $this->primary_key_field();
                /** @param \ORM\Field\Field $field  */
                foreach ($scheme as $field_name => $field) {
                    $field->init($this, $field_name);
                    $fields[$field_name] = $field;
                }

                $this->fields = $fields;
            }

            return $this->fields;
        }

        /**
         * Возвращает экземпляр поля с указанным именем
         * @param string $name Имя поля
         * @return mixed
         */
        public function get_field($name)
        {
            if (array_key_exists($name, $this->fields())) {
                return $this->fields()[$name];
            }

            return null;
        }

        /**
         * Формирование строки SQL для создания таблицы в БД
         * @return string
         */
        public function sql_create_table()
        {
            $sql_fragments = [];
            $sql_fragments[] = 'CREATE TABLE IF NOT EXISTS';
            $sql_fragments[] = sprintf('`%s`', $this->table_name());
            $sql_fragments[] = sprintf('(%s)', $this->sql_fields());

            return implode(' ', $sql_fragments);
        }

        /**
         * Возвращает строку в SQL для формирования списка полей
         * @return string
         */
        protected function sql_fields()
        {
            $fields = $this->fields();
            $sql_fields = [];

            // Добавление в список полей
            foreach ($fields as $field_name => $field) {
                $sql_fields[] = $field->to_sql();
            }

            // Добавление формирования первичного ключа
            $sql_fields[] = sprintf('PRIMARY KEY (`%s`)', $this->primary_key);

            // Добавление уникальных ключей
            if (count($this->unique_keys)) {
                $uniq_keys = [];
                foreach ($this->unique_keys as $uniq) {
                    $uniq_keys[] = sprintf('`%s`', $uniq);
                }

                $sql_fields[] = sprintf(
                    'UNIQUE KEY `%s` (%s),',
                    $this->random_name($this->table_name()),
                    implode(',', $uniq_keys)
                );

            }

            return implode(',', $sql_fields);

        }

        /**
         * Возвращает имя таблицы
         * В случае, если имя таблицы не задано - используется имя, основанное на имени модели
         * @return null|string
         */
        public function table_name()
        {
            $class_name = explode('\\', get_class($this));
            return !is_null($this->table_name) ? $this->table_name : strtolower(array_pop($class_name));
        }

        /**
         * Описание поля, содержащего первичный ключ
         * Существует возможность переопределения в дочерних классах для реализации собственных полей
         * @return PrimaryKeyField
         */
        public function primary_key_field()
        {
            return new PrimaryKeyField();
        }

        /**
         * Возвращает случайно сгенерированную строку,
         * содержащую переданное значение в качестве подстроки
         * @param string $name
         * @return string
         */
        protected function random_name($name)
        {
            $rnd = md5($name . time());
            return sprintf('%s_%s', $name, substr($rnd, 0, 7));
        }

    } 