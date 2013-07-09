<?php

    namespace DMF\Core\Model;

    use DMF\Core\Component\Component;
    use DMF\Core\Model\Exception\DBError;
    use DMF\Core\OS\File;
    use DMF\Core\OS\OS;
    use DMF\Core\Storage\Config;

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
         * Автосоздание таблицы для указанной модели в БД
         * ВНИМАНИЕ! Создает запрос для проверки наличия указанной таблицы в БД при каждой инициализации модели,
         * будучи установленной в true!
         * @var bool Требуется ли создавать таблицу указанной модели в БД автоматически в случае ее отсутствия
         */
        public $table_auto_create = false;

        /**
         * Инициализация модели
         */
        public function __construct()
        {
            parent::__construct();
            if ($this->table_auto_create && !$this->table_exists()) {
                $this->create_table();
            }
        }

        /**
         * Обновление структуры таблицы в БД
         */
        public function update_table()
        {
            // TODO: Реализовать щадящее обновление структуры таблицы
            $check_table = self::$db->query('SHOW TABLES LIKE :table', ['table' => $this->table_name()]);
            if ($check_table->num_rows() == 1) {
                $this->drop_table();
            }
            $this->create_table();
        }

        /**
         * Возвращает строку с именем таблицы БД
         * @return string
         */
        public function table_name()
        {
            if (is_null($this->table_name)) {
                return $this->table_prefix() . strtolower($this->get_class_name()) . 's';
            }
            return $this->table_prefix() . $this->table_name;
        }

        /**
         * Возвращает строку с префиксом к имени таблицы
         * Возможно переопределить в дочернем классе для использования собственных префиксов
         * @return mixed
         */
        protected function table_prefix()
        {
            return $this->config('database')['prefix'];
        }

        /**
         * Проверка наличия таблицы данной модели в БД
         * @return bool
         */
        public function table_exists()
        {
            $result = self::$db->query('SHOW TABLES LIKE :table_name', ['table_name' => $this->table_name()]);
            return !!($result->num_rows() == 1);
        }

        /**
         * Удаление таблицы
         * @return bool
         */
        public function drop_table()
        {
            self::$db->exec('DROP TABLE IF EXISTS ' . $this->table_name());
        }

        /**
         * Создание новой таблицы
         * @param bool $load_fixtures Требуется ли подгружать фикстуры при создании таблицы
         */
        public function create_table($load_fixtures = false)
        {
            // Выполнение запроса на создание таблицы в БД
            self::$db->exec($this->generate_sql_for_table());
            if ($load_fixtures) {
                // Загрузка фикстур
                $this->load_fixtures();
            }
        }

        /**
         * Возвращает SQL код, необходимый для создания текущей схемы таблицы
         */
        public function generate_sql_for_table()
        {
            $query = 'CREATE TABLE IF NOT EXISTS `' . $this->table_name() . '` (' . PHP_EOL;
            $fields = $this->sql_from_fields();
            $query .= implode(',' . PHP_EOL, $fields) . PHP_EOL . ') ENGINE=InnoDB DEFAULT CHARSET=utf8';
            return $query;
        }

        /**
         * Возвращает массив полей в таблице
         * @return array
         */
        protected function sql_from_fields()
        {
            $scheme = $this->scheme();
            $fields = [];
            /** @var $field_object \DMF\Core\Model\Field\BaseField */
            foreach ($scheme as $field_name => $field_object) {
                $fields[] = trim($field_object->create_sql($field_name));
            }
            return $fields;
        }

        /**
         * Возвращает текущую схему БД
         * @return array
         */
        public function scheme()
        {
            return [];
        }

        /**
         * Поиск и загрузка данных из фикстур
         * @throws Exception\DBError
         */
        public function load_fixtures()
        {
            // Имя файл фикстуры, генерируется из имени модуля и имени модели
            $fixture_name = strtolower($this->get_module_name()) . '__' . strtolower($this->get_class_name()) . '.json';
            // Полный путь до файла с фикстурой
            $file = new File(DATA_PATH . 'fixtures' . _SEP . $fixture_name);
            $fixture = $file->open('r')->read();
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
                } catch (\Exception $e) {
                    // Откатываемся, если обнаружили ошибку
                    self::$db->rollBack();
                    throw new DBError('[DB] Произошла ошибка при выполнении запроса к БД, транзакция будет отменена.
                        Текст ошибки: ' . $e->getMessage());
                }
            }
        }

        /**
         * Создание нового объекта в БД
         * @param array $data Данные для создания объекта
         * @return int ID созданной записи
         */
        public function create($data)
        {
            $fields = $this->fields();
            $sql = [];
            $params = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $fields)) {
                    $sql[] = $key . '=:u_' . $key;
                    $params['u_' . $key] = $value;
                }
            }
            $result_code = 'INSERT INTO ' . $this->table_name() . ' SET ' . implode(', ', $sql);
            $query = self::$db->query($result_code, $params);
            return $query->last_insert_id();
        }

        /**
         * Возвращает массив, содержащий имена полей БД
         * @return array
         */
        public function fields()
        {
            return array_keys($this->scheme());
        }

        /**
         * Получение данных из БД и выгрузка их в файл фикстур
         */
        public function dump_fixtures()
        {
            $fixture_name = strtolower($this->get_module_name()) . '__' . strtolower($this->get_class_name()) . '.json';
            $data = $this->dump_data();
            $file = new File(DATA_PATH . 'fixtures' . _SEP . $fixture_name);
            $file->open('w+')->block()->write(json_encode($data, JSON_PRETTY_PRINT))->unblock()->close();
        }

        /**
         * Дамп данных из БД за вычетом первичного ключа
         * @param null|int $limit Предел выборки данных из БД
         * @return mixed
         */
        protected function dump_data($limit = null)
        {
            $fixture = [];
            // Запрос на выборку элементов из БД
            $query = 'SELECT * FROM ' . $this->table_name() . (is_null($limit) ? '' : ' LIMIT ' . (int)$limit);
            $data = self::$db->query($query)->fetch_all();
            // Имя первичного ключа, для его удаления
            $primary_key = $this->primary_key();
            foreach ($data as $element) {
                unset($element[$primary_key]);
                $fixture[] = $element;
            }
            return $fixture;
        }

        /**
         * Возвращает имя первичного ключа таблицы
         * @return bool|int|string
         */
        public function primary_key()
        {
            foreach ($this->scheme() as $field_name => $field_object) {
                if ($field_object instanceof \DMF\Core\Model\Field\PrimaryKeyField) {
                    return $field_name;
                }
            }
            return 'id';
        }

        /**
         * Возвращает все записи в указанной модели
         * @param string|array $fields Список возвращаемых полей
         * @return EntityCollection Коллекция всех сущностей модели
         */
        public function get_all($fields = '*')
        {
            if (is_array($fields)) {
                $fields = implode(', ', $fields);
            }
            $data = self::$db->query('SELECT ' . $fields . ' FROM ' . $this->table_name());
            return new EntityCollection($this, $data->fetch_all());
        }

        /**
         * Возвращает пустую коллекцию указанной модели
         * @return EntityCollection Пустая коллекция сущностей
         */
        public function get_empty()
        {
            return new EntityCollection($this);
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
            $select_fields = (count($fields) > 0) ? $fields : $this->fields();
            // Выполнение запроса к БД
            $data = self::$db->query(
                'SELECT ' . implode(', ', $select_fields) . ' FROM ' . $this->table_name() . ' WHERE '
                . $this->primary_key() . '=:pk LIMIT 1',
                ['pk' => (int)$pk]
            );
            // Если элемент обнаружен, то добавляем его в сущность
            if ($data->num_rows() == 1) {
                $entity = $data->fetch_one();
                $entity_namespace = $this->entity_namespace();
                return new $entity_namespace($this, $entity);
            }
            return false;
        }

        /**
         * Возвращает полный путь до класса сущности
         * @return string
         */
        public function entity_namespace()
        {
            $segments = explode('.', $this->entity_name);
            if (count($segments) == 1) {
                return $this->get_module()->namespace . '\\Model\\' . $segments[0];
            } else {
                if ($segments[0] == 'DMF') {
                    return '\\DMF\\Core\\Model\\Entity';
                }
                return $this->get_module($segments[0])->namespace . '\\Model\\' . $segments[1];
            }
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
            $select_fields = (count($fields) > 0) ? $fields : $this->fields();
            // Формирование SQL для условия выборки
            $sql_condition = $this->generate_condition_sql($condition);
            // Выполнение запроса к БД
            $sql = 'SELECT ' . implode(', ', $select_fields) . ' FROM ' . $this->table_name()
                    . $sql_condition['query']
                    . $this->generate_order_sql($order_by)
                    . $this->generate_limit_sql($limit);
            // Препарирование параметров выборки
            $data = self::$db->query($sql, $sql_condition['params']);
            // Если найдена хотя бы 1 запись,
            // то создаем из нее объект сущности и добавляем в коллекцию сущностей
            if ($data->num_rows() > 0) {
                return new EntityCollection($this, $data->fetch_all());
            }
            return new EntityCollection($this);
        }

        /**
         * Генерация параметризированного SQL кода
         *
         * @param array $condition Список параметров для выборки
         * @return array
         */
        protected function generate_condition_sql($condition = [])
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
                } elseif (substr($data[0], 0, 1) == '~') {
                    $precond = ' OR ';
                    $field_name = substr($data[0], 1);
                } else {
                    $precond = ' AND ';
                }
                /** Создание массива значений для выборки */
                if (count($data) == 1) {
                    $result['queries'][] = $precond . $field_name . '=:' . $field_name;
                    $result['params'][$field_name] = $value;
                } else {
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
                            } else {
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
         *
         * @param string|array $order_by
         * @return string
         */
        protected function generate_order_sql($order_by)
        {
            if (count($order_by) <= 0) {
                return '';
            }
            if (is_string($order_by)) {
                if (substr($order_by, 0, 1) == '~') {
                    $order_direction = 'DESC';
                    $order_by = substr($order_by, 1);
                } else {
                    $order_direction = 'ASC';
                }
                return ' ORDER BY ' . $order_by . ' ' . $order_direction;
            } else {
                $data = [];
                foreach ($order_by as $field) {
                    if (substr($field, 0, 1) == '~') {
                        $order_direction = 'DESC';
                        $field = substr($field, 1);
                    } else {
                        $order_direction = 'ASC';
                    }
                    $data[] = $field . ' ' . $order_direction;
                }
                return ' ORDER BY ' . implode(', ', $data);
            }
        }

        /**
         * Генерация SQL кода для реализации лимита выборки
         *
         * @param string|int $limit Лимит выборки
         * @return string
         */
        protected function generate_limit_sql($limit)
        {
            if (is_null($limit)) {
                return '';
            }
            return ' LIMIT ' . $limit;
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
            $sql_condition = $this->generate_condition_sql($condition);
            $sql = 'SELECT COUNT(' . $fields . ') AS count FROM ' . $this->table_name() . $sql_condition['query'];
            return self::$db->query($sql, $sql_condition['params'])->fetch_one()['count'];
        }

        /**
         * Обновление объекта по его первичному ключу
         * Данные, передаваемые в data будут заменять значения полей объекта,
         * имеющие имена полей, соответствующие ключам переданных данных.
         *
         * @param int   $pk   ID объекта
         * @param array $data Данные для обновления
         */
        public function update_by_pk($pk, $data)
        {
            $fields = $this->fields();
            $sql = [];
            $params = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $fields)) {
                    $sql[] = $key . '=:u_' . $key;
                    $params['u_' . $key] = $value;
                }
            }
            $params['pk'] = $pk;
            $result_code = 'UPDATE ' . $this->table_name() . ' SET ' . implode(', ', $sql)
                    . ' WHERE ' . $this->primary_key() . '=:pk';
            self::$db->query($result_code, $params)->send();
        }

        /**
         * Обновление объектов по определенному условию
         * @param array $condition Условия для выборки
         * @param array $data      Данные для обновления
         */
        public function update_by_condition($condition, $data)
        {
            $cond = $this->generate_condition_sql($condition);
            $fields = $this->fields();
            $sql = [];
            $params = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $fields)) {
                    $sql[] = $key . '=:u_' . $key;
                    $params['u_' . $key] = $value;
                }
            }
            $result_params = array_merge($params, $cond['params']);
            $result_code = 'UPDATE ' . $this->table_name() . ' SET ' . implode(', ', $sql) . $cond['query'];
            self::$db->query($result_code, $result_params)->send();
        }

    }
