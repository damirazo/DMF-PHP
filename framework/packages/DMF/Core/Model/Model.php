<?php

    namespace DMF\Core\Model;

    use DMF\Core\Component\Component;
    use DMF\Core\Storage\Config;
    use DMF\Core\OS\OS;
    use DMF\Core\Model\Exception\DBError;

    /**
     * Базовая модель
     */
    class Model extends Component
    {

        /** @var null|string Имя таблицы в БД */
        public $table_name = null;

        /** @var string Имя класса для возвращаемой выборкой из БД сущности */
        public $entity_name = 'DMF.Entity';

        /**
         * Возвращает текущую схему БД
         * @return array
         */
        public function _scheme()
        {
            return [];
        }

        /**
         * Возвращает строку с префиксом к имени таблицы
         * Возможно переопределить в дочернем классе для использования собственных префиксов
         * @return mixed
         */
        protected function _get_table_prefix()
        {
            return $this->config('database')['prefix'];
        }

        /**
         * Возвращает строку с именем таблицы БД
         * @return string
         */
        public function _get_table_name()
        {
            if (is_null($this->table_name)) {
                return $this->_get_table_prefix() . strtolower($this->get_class_name()) . 's';
            }
            return $this->_get_table_prefix() . $this->table_name;
        }

        /**
         * Возвращает массив полей в таблице
         * @return array
         */
        protected function _generate_sql_for_model_fields()
        {
            $scheme = $this->_scheme();
            $fields = [];
            /** @var $field_object \DMF\Core\Model\Field\BaseField */
            foreach ($scheme as $field_name => $field_object) {
                $fields[] = trim($field_object->create_sql($field_name));
            }
            return $fields;
        }

        /**
         * Обновление структуры таблицы в БД
         */
        public function _update_table()
        {
            $check_table = self::$db->query('SHOW TABLES LIKE :table', ['table' => $this->_get_table_name()]);
            if ($check_table->num_rows() == 1) {
                $this->_drop_table();
            }
            $this->_create_table();
        }

        /**
         * Создание новой таблицы
         */
        public function _create_table()
        {
            // Выполнение запроса на создание таблицы в БД
            self::$db->exec($this->_generate_sql_for_table());
            // Загрузка фикстур
            $this->_load_fixtures();
        }

        /**
         * Поиск и загрузка данных из фикстур
         * @throws Exception\DBError
         */
        public function _load_fixtures()
        {
            // Имя файл фикстуры, генерируется из имени модуля и имени модели
            $fixture_name = strtolower($this->get_module_name()) . '__' . strtolower($this->get_class_name()) . '.json';
            // Полный путь до файла с фикстурой
            $fixture = OS::file_data(DATA_PATH . 'fixtures' . _SEP . $fixture_name, false, false);
            // Если файл с фикстурой отсутствует, то ничего не делаем
            if ($fixture !== false) {
                // Данные из файла, преобразованные в PHP массив
                $data = json_decode($fixture);
                // Выполнение сохранения объектов из БД в транзакции с откатом при ошибке
                try {
                    self::$db->beginTransaction();
                    // Обходим массив и добавляем каждый элемент в БД
                    foreach ($data as $element) {
                        $this->create($element);
                    }
                    self::$db->commit();
                }
                catch (\Exception $e) {
                    // Откатываемся, если обнаружили ошибку
                    self::$db->rollBack();
                    throw new DBError('[DB] Произошла ошибка при выполнении запроса к БД, транзакция будет отменена.
                        Текст ошибки: ' . $e->getMessage());
                }
            }
        }

        /**
         * Получение данных из БД и выгрузка их в файл фикстур
         */
        public function _dump_fixtures()
        {
            $fixture_name = strtolower($this->get_module_name()) . '__' . strtolower($this->get_class_name()) . '.json';
            $data = $this->_dump_data(10);
            OS::file(DATA_PATH . 'fixtures' . _SEP . $fixture_name)->write(json_encode($data, JSON_PRETTY_PRINT));
        }

        /**
         * Дамп данных из БД за вычетом первичного ключа
         * @param null|int $limit Предел выборки данных из БД
         * @return mixed
         */
        protected function _dump_data($limit = null)
        {
            $fixture = [];
            // Запрос на выборку элементов из БД
            $query = 'SELECT * FROM ' . $this->_get_table_name() . (is_null($limit) ? '' : ' LIMIT ' . (int)$limit);
            $data = self::$db->query($query)->fetch_all();
            // Имя первичного ключа, для его удаления
            $primary_key = $this->_get_primary_key_field_name();
            foreach ($data as $element) {
                unset($element[$primary_key]);
                $fixture[] = $element;
            }
            return $fixture;
        }

        /**
         * Удаление таблицы
         * @return bool
         */
        public function _drop_table()
        {
            self::$db->exec('DROP TABLE IF EXISTS ' . $this->_get_table_name());
        }

        /**
         * Возвращает SQL код, необходимый для создания текущей схемы таблицы
         */
        public function _generate_sql_for_table()
        {
            $query = 'CREATE TABLE IF NOT EXISTS `' . $this->_get_table_name() . '` (' . PHP_EOL;
            $fields = $this->_generate_sql_for_model_fields();
            $query .= implode(',' . PHP_EOL, $fields) . PHP_EOL . ') ENGINE=InnoDB DEFAULT CHARSET=utf8';
            return $query;
        }

        /**
         * Возвращает имя первичного ключа таблицы
         * @return bool|int|string
         */
        public function _get_primary_key_field_name()
        {
            foreach ($this->_scheme() as $field_name => $field_object) {
                if ($field_object instanceof \DMF\Core\Model\Field\PrimaryKeyField) {
                    return $field_name;
                }
            }
            return 'id';
        }

        /**
         * Генерация параметризированного SQL кода
         * @param array $condition Список параметров для выборки
         * @return array
         */
        protected function _get_sql_from_condition($condition = [])
        {
            if (count($condition) <= 0) {
                return ['query' => '', 'params' => []];
            }
            $result = [
                'queries' => [],
                'params'  => []
            ];
            $i = 0;
            foreach ($condition as $field_cond => $value) {
                $data = explode('__', $field_cond);
                $field_name = $data[0];
                /** Обработка логического условия */
                if ($i == 0) {
                    $precond = '';
                }
                elseif (substr($data[0], 0, 1) == '~') {
                    $precond = ' OR ';
                    $field_name = substr($data[0], 1);
                }
                else {
                    $precond = ' AND ';
                }
                /** Создание массива значений для выборки */
                if (count($data) == 1) {
                    $result['queries'][] = $precond . $field_name . '=:' . $field_name;
                    $result['params'][$field_name] = $value;
                }
                else {
                    $cond = $data[1];
                    switch ($cond) {
                        /** Проверка на точное совпадение */
                        case 'equal':
                            $result['queries'][] = $precond . $field_name . '=:' . $field_name;
                            $result['params'][$field_name] = $value;
                            break;
                        /** Проверка на точное различие */
                        case 'not_equal':
                            $result['queries'][] = $precond . $field_name . '!=:' . $field_name;
                            $result['params'][$field_name] = $value;
                            break;
                        /** Проверка на больше */
                        case 'gt':
                            $result['queries'][] = $precond . $field_name . '>:' . $field_name;
                            $result['params'][$field_name] = $value;
                            break;
                        /** Проверка на больше или равно */
                        case 'gte':
                            $result['queries'][] = $precond . $field_name . '>=:' . $field_name;
                            $result['params'][$field_name] = $value;
                            break;
                        /** Проверка на меньше */
                        case 'lt':
                            $result['queries'][] = $precond . $field_name . '<:' . $field_name;
                            $result['params'][$field_name] = $value;
                            break;
                        /** Проверка на меньше или равно */
                        case 'lte':
                            $result['queries'][] = $precond . $field_name . '<=:' . $field_name;
                            $result['params'][$field_name] = $value;
                            break;
                        /** Проверка на то, что значение начинается с нужной подстроки */
                        case 'startswith':
                            $result['queries'][] = $precond . $field_name . ' LIKE :' . $field_name;
                            $result['params'][$field_name] = $value . '%';
                            break;
                        /** Проверка на то, что значение заканчивается нужной подстрокой */
                        case 'endswith':
                            $result['queries'][] = $precond . $field_name . ' LIKE :' . $field_name;
                            $result['params'][$field_name] = '%' . $value;
                            break;
                        /** Проверка на то, что значение содержит нужную подстроку */
                        case 'contains':
                            $result['queries'][] = $precond . $field_name . ' LIKE :' . $field_name;
                            $result['params'][$field_name] = '%' . $value . '%';
                            break;
                        /** Проверка на то, что значение есть в списке */
                        case 'in':
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }
                            $result['queries'][] = $precond . $field_name . ' IN(:' . $field_name . ')';
                            $result['params'][$field_name] = implode(', ', $value);
                            break;
                        /** Проверка на то, что значение равно или не равно нулю */
                        case 'is_null':
                            if ($value === true) {
                                $result['queries'][] = $precond . $field_name . ' IS NULL ';
                            }
                            else {
                                $result['queries'][] = $precond . $field_name . ' IS NOT NULL ';
                            }
                            break;
                        default:
                            $result['queries'][] = $precond . $field_name . '=:' . $field_name;
                            $result['params'][$field_name] = $value;
                            break;
                    }
                }
                $i++;
            }
            return [
                'query'  => ' WHERE ' . implode('', $result['queries']),
                'params' => $result['params']
            ];
        }

        /**
         * Генерация SQL кода для сортировки вывода
         * @param string|array $order_by
         * @return string
         */
        protected function _get_sql_from_order_by($order_by)
        {
            if (count($order_by) <= 0) {
                return '';
            }
            if (is_string($order_by)) {
                if (substr($order_by, 0, 1) == '~') {
                    $order_direction = 'DESC';
                    $order_by = substr($order_by, 1);
                }
                else {
                    $order_direction = 'ASC';
                }
                return ' ORDER BY ' . $order_by . ' ' . $order_direction;
            }
            else {
                $data = [];
                foreach ($order_by as $field) {
                    if (substr($field, 0, 1) == '~') {
                        $order_direction = 'DESC';
                        $field = substr($field, 1);
                    }
                    else {
                        $order_direction = 'ASC';
                    }
                    $data[] = $field . ' ' . $order_direction;
                }
                return ' ORDER BY ' . implode(', ', $data);
            }
        }

        /**
         * Генерация SQL кода для реализации лимита выборки
         * @param string|int $limit Лимит выборки
         * @return string
         */
        protected function _get_sql_from_limit($limit)
        {
            if (is_null($limit)) {
                return '';
            }
            return ' LIMIT ' . $limit;
        }

        /**
         * Возвращает массив, содержащий имена полей БД
         * @return array
         */
        protected function _get_table_fields()
        {
            return array_keys($this->_scheme());
        }

        /**
         * Возвращает полный путь до класса сущности
         * @return string
         */
        protected function _get_entity_namespace()
        {
            $segments = explode('.', $this->entity_name);
            if (count($segments) == 1) {
                return $this->get_module()->namespace . '\\Model\\' . $segments[0];
            }
            else {
                if ($segments[0] == 'DMF') {
                    return '\\DMF\\Core\\Model\\Entity';
                }
                return $this->get_module($segments[0])->namespace . '\\Model\\' . $segments[1];
            }
        }

        /**
         * Возвращает один объект, выбранный по первичному ключу
         * @param int   $pk     Первичный ключ
         * @param array $fields Список выбираемых из таблицы полей
         * @return Entity Сущность
         */
        public function get_by_pk($pk, $fields = [])
        {
            // Список выбираемых из таблицы полей (по умолчанию все поля)
            $select_fields = (count($fields) > 0) ? $fields : $this->_get_table_fields();
            // Выполнение запроса к БД
            $data = self::$db->query(
                'SELECT ' . implode(', ', $select_fields) . ' FROM ' . $this->_get_table_name() . ' WHERE '
                        . $this->_get_primary_key_field_name() . '=:pk LIMIT 1',
                ['pk' => (int)$pk]
            );
            // Если элемент обнаружен, то добавляем его в сущность
            if ($data->num_rows() == 1) {
                $entity = $data->fetch_one();
                $entity_namespace = $this->_get_entity_namespace();
                return new $entity_namespace($this, $entity);
            }
            return false;
        }

        /**
         * Выборка массива объектов для определенной страницы
         * @param array $order_by  Поле для сортировки
         * @param array $condition Условия для выборки
         * @param int   $limit     Количество объектов
         * @param array $fields    Список выбираемых полей
         * @return EntityCollection Коллекция сущностей
         */
        public function get_by_condition($condition = [], $order_by = [], $limit = null, $fields = [])
        {
            // Список выбираемых из таблицы полей (по умолчанию все поля)
            $select_fields = (count($fields) > 0) ? $fields : $this->_get_table_fields();
            // Формирование SQL для условия выборки
            $sql_condition = $this->_get_sql_from_condition($condition);
            // Выполнение запроса к БД
            $sql = 'SELECT ' . implode(', ', $select_fields) . ' FROM ' . $this->_get_table_name()
                    . $sql_condition['query']
                    . $this->_get_sql_from_order_by($order_by)
                    . $this->_get_sql_from_limit($limit);
            // Препарирование параметров выборки
            $data = self::$db->query($sql, $sql_condition['params']);
            // Если найдена хотя бы 1 запись,
            // то создаем из нее объект сущности и добавляем в коллекцию сущностей
            if ($data->num_rows() > 0) {
                $entities = $data->fetch_all();
                $collection = new EntityCollection($this->_get_table_name());
                foreach ($entities as $element) {
                    $entity_namespace = $this->_get_entity_namespace();
                    $entity = new $entity_namespace($this, $element);
                    $collection->add_entity($entity);
                }
                return $collection;
            }
            return new EntityCollection($this->_get_table_name());
        }

        /**
         * Возвращает количество записей в БД с указанным условием
         * @param string|array $fields    Поле/поля для выборки
         * @param array        $condition Условия для выборки
         * @return int Количество записей, подходящих под условия
         */
        public function get_count($fields = '*', $condition = [])
        {
            if (is_array($fields)) {
                $fields = implode(', ', $fields);
            }
            // Формирование SQL для условия выборки
            $sql_condition = $this->_get_sql_from_condition($condition);
            $sql = 'SELECT COUNT(' . $fields . ') AS count FROM ' . $this->_get_table_name() . $sql_condition['query'];
            return self::$db->query($sql, $sql_condition['params'])->fetch_one()['count'];
        }

        /**
         * Обновление объекта по его первичному ключу
         * @param array $data Данные для обновления
         * @param int   $pk   Первичный ключ
         */
        public function update_by_pk($data, $pk)
        {
            $fields = $this->_get_table_fields();
            $sql = [];
            $params = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $fields)) {
                    $sql[] = $key . '=:u_' . $key;
                    $params['u_' . $key] = $value;
                }
            }
            $params['pk'] = $pk;
            $result_code = 'UPDATE ' . $this->_get_table_name() . ' SET ' . implode(', ', $sql)
                    . ' WHERE ' . $this->_get_primary_key_field_name() . '=:pk';
            self::$db->query($result_code, $params)->send();
        }

        /**
         * Обновление объектов по определенному условию
         * @param array $data      Данные для обновления
         * @param array $condition Условия для выборки
         */
        public function update_by_condition($data, $condition)
        {
            $cond = $this->_get_sql_from_condition($condition);
            $fields = $this->_get_table_fields();
            $sql = [];
            $params = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $fields)) {
                    $sql[] = $key . '=:u_' . $key;
                    $params['u_' . $key] = $value;
                }
            }
            $result_params = array_merge($params, $cond['params']);
            $result_code = 'UPDATE ' . $this->_get_table_name() . ' SET ' . implode(', ', $sql) . $cond['query'];
            self::$db->query($result_code, $result_params)->send();
        }

        /**
         * Создание нового объекта в БД
         * @param array $data Данные для создания объекта
         */
        public function create($data)
        {
            $fields = $this->_get_table_fields();
            $sql = [];
            $params = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $fields)) {
                    $sql[] = $key . '=:u_' . $key;
                    $params['u_' . $key] = $value;
                }
            }
            $result_code = 'INSERT INTO ' . $this->_get_table_name() . ' SET ' . implode(', ', $sql);
            self::$db->query($result_code, $params);
        }

    }
