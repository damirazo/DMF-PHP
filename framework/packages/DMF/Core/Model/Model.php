<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model;

    use DMF\Core\Component\Component;
    use DMF\Core\Model\Exception\DBError;
    use DMF\Core\Model\Exception\RecordDoesNotExists;
    use DMF\Core\OS\File;
    use DMF\Core\OS\OS;
    use DMF\Core\Storage\Config;

    /**
     * Class Model
     * Модель БД
     *
     * @package DMF\Core\Model
     */
    class Model extends Component
    {

        /** @var null|string Имя таблицы в БД */
        public $table_name = null;
        /** @var null|string Префикс для имени текущей таблицы в БД. По умолчанию используется глобальный. */
        public $table_prefix = null;
        /** @var string Имя класса для возвращаемой выборкой из БД сущности */
        public $entity_name = 'DMF.Entity';
        /**
         * Автосоздание таблицы для указанной модели в БД
         * ВНИМАНИЕ! Создает запрос для проверки наличия указанной таблицы в БД при каждой инициализации модели,
         * будучи установленной в true!
         *
         * @var bool Требуется ли создавать таблицу указанной модели в БД автоматически в случае ее отсутствия
         */
        public $auto_create = false;
        /** @var string Движок, используемый при создании таблиц БД */
        public $table_engine = 'InnoDB';
        /** @var string Кодировка для таблиц БД по умолчанию */
        public $table_encoding = 'utf8';

        /**
         * Инициализация модели
         */
        public function __construct()
        {
            parent::__construct();
            // Создание таблицы
            if ($this->auto_create && !$this->table_exists()) {
                $this->create_table();
            }
        }

        //#############################################################################################################
        //# Описание модели
        //#############################################################################################################

        /**
         * Возвращает текущую схему БД
         * @return array
         */
        public function scheme()
        {
            return [];
        }

        /**
         * Возвращает строку с именем таблицы БД
         * @return string
         */
        public function table_name()
        {
            if (is_null($this->table_name)) {
                return $this->table_prefix() . strtolower($this->class_name()) . 's';
            }
            return $this->table_prefix() . $this->table_name;
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
         * Возвращает массив, содержащий имена полей БД
         * @return array
         */
        public function fields()
        {
            return array_keys($this->scheme());
        }

        /**
         * Возвращает полный путь до класса сущности
         * @return string
         */
        public function entity_namespace()
        {
            $segments = explode('.', $this->entity_name);
            if (count($segments) == 1) {
                return self::$app->get_module_by_name()->namespace . '\\Entity\\' . $segments[0];
            } else {
                // Возвращаем сущность, используемую по умолчанию
                if ($segments[0] == 'DMF') {
                    return '\\DMF\\Core\\Model\\Entity';
                }
                return self::$app->get_module_by_name($segments[0])->namespace . '\\Entity\\' . $segments[1];
            }
        }

        /**
         * Возвращает строку с префиксом к имени таблицы
         * Возможно переопределить в дочернем классе для использования собственных префиксов
         * @return mixed
         */
        protected function table_prefix()
        {
            if (is_null($this->table_prefix)) {
                return $this->config('database')['prefix'];
            }
            return $this->table_prefix;
        }

        //#############################################################################################################
        //# Работа с таблицей в БД
        //#############################################################################################################

        /**
         * Обновление структуры таблицы в БД
         * @param bool $prepare_migration Применить миграции, в случае необходимости
         * @return array
         */
        public function update_table($prepare_migration = false)
        {
            // Проверяем наличие указанной таблицы в БД
            $check_table = self::$db->query('SHOW TABLES LIKE :table', ['table' => $this->table_name()]);
            $statement = ['status' => true, 'message' => ''];
            // Если таблица отсутствует, то создаем ее
            if ($check_table->num_rows() < 1) {
                // Фикстуры при создании таблицы будут подгружаться самостоятельно
                $this->create_table();
                $this->create_migration();
            } else {
                // Объект модуля, в котором определена текущая модель
                $module = $this->module();
                $migrations_dir = $module->path . 'Model' . _SEP . 'migrations' . _SEP;
                // Проверяем была ли уже создана папка с миграциями
                if (OS::dir_exists($migrations_dir)) {
                    $migration_file = $migrations_dir . $this->class_name() . '.json';
                    if (OS::file_exists($migration_file)) {
                        $file = new File($migration_file);
                        $data = $file->open()->read_as_json();
                        $changes = $this->check_table($data);

                        if ($changes['total'] > 0) {
                            if ($prepare_migration) {
                                $this->add_fields($changes['added_fields']);
                                $this->remove_fields(array_keys($changes['removed_fields']));
                                $this->change_fields($changes['changed_fields']);
                                $this->create_migration();
                            } else {
                                $statement['status'] = false;
                                $statement['message'] = sprintf(
                                    'Обнаружено %d изменений в таблице. Из них:' . PHP_EOL
                                    . '\tДобавлено новых полей: %d' . PHP_EOL
                                    . '\tИзменено полей: %d' . PHP_EOL
                                    . '\tУдалено полей: %d' . PHP_EOL
                                    . 'Введите Yes для применения данных изменений, в противном случае введите N.'
                                );
                            }
                        }
                    }
                }
            }
            return $statement;
        }

        /**
         * Проверка текущей схемы модели и сравнение ее с состоянием, сохраненным в миграции
         * @param array $migration_data Схема миграции
         * @return array
         */
        public function check_table($migration_data)
        {
            // Массив для хранения информации об изменениях в схеме модели
            $changes = [
                // Общее число изменений
                'total'          => 0,
                // Список добавленных полей
                'added_fields'   => [],
                // Список удаленных полей
                'removed_fields' => [],
                // Список измененных полей
                'changed_fields' => [],
            ];
            /** @var $field_data \DMF\Core\Model\Field\BaseField */
            foreach ($this->scheme() as $field_name => $field_data) {
                // Проверка поля из схемы модели на существование
                if (!isset($migration_data->{$field_name})) {
                    $changes['total'] += 1;
                    $changes['added_fields'][$field_name] = $field_data;
                    continue;
                }
                // Проверка поля на изменение
                $migration_field = $migration_data->{$field_name};
                if ($migration_field->hash != $field_data->hash($field_name)) {
                    $changes['total'] += 1;
                    $changes['changed_fields'][$field_name] = $field_data;
                    continue;
                }
            }
            // Проверка поля на удаление
            foreach ($migration_data as $field_name => $field_data) {
                if (!array_key_exists($field_name, $this->scheme())) {
                    $changes['total'] += 1;
                    $changes['removed_fields'][$field_name] = $field_data;
                    continue;
                }
            }
            return $changes;
        }

        /**
         * Создание файла миграции для текущей схемы модели
         */
        public function create_migration()
        {
            $scheme = $this->scheme();
            $result = [];
            /** @var $field_object \DMF\Core\Model\Field\BaseField */
            foreach ($scheme as $field_name => $field_object) {
                $params = $field_object->params;
                $result[$field_name] = $params;
                $result[$field_name]['hash'] = md5($field_name . '+' . serialize($params));
                $result[$field_name]['type'] = $field_object->class_namespace();
            }
            $data = json_encode($result, JSON_PRETTY_PRINT);
            // Объект модуля, в котором определена текущая модель
            $module = $this->module();
            $migrations_dir = $module->path . 'Model' . _SEP . 'migrations' . _SEP;
            if (!OS::dir_exists($migrations_dir)) {
                mkdir($migrations_dir);
            }
            $migration_file = $migrations_dir . $this->class_name() . '.json';
            $file = new File($migration_file);
            $file->open('w+')->block()->write($data)->unblock()->close();
        }

        /**
         * Создание новой таблицы
         */
        public function create_table()
        {
            // Выполнение запроса на создание таблицы в БД
            self::$db->exec($this->generate_sql_for_table());
        }

        /**
         * Добавление в таблицу полей, указанных в параметре
         * @param array $data Список добавляемых полей и их параметров
         * @return string
         */
        public function add_fields($data)
        {
            if (count($data) > 0) {
                $result_fields = [];
                /** @var $field_data \DMF\Core\Model\Field\BaseField */
                foreach ($data as $field_name => $field_data) {
                    $result_fields[] = $field_data->sql($field_name);
                }
                $query = 'ALTER TABLE `' . $this->table_name() . '` ADD ' . implode(', ADD ', $result_fields) . ';';
                self::$db->query($query);
            }
        }

        /**
         * Удаление из таблицы полей с указанными именами
         * @param array $data Список имен удаляемых полей
         * @return string
         */
        public function remove_fields($data)
        {
            if (count($data) > 0) {
                $query = 'ALTER TABLE `' . $this->table_name() . '` DROP ' . implode(', DROP ', $data) . ';';
                self::$db->query($query);
            }
        }

        /**
         * Изменение полей в таблице
         * @param array $data Список значений измененных полей
         * @return string
         */
        public function change_fields($data)
        {
            if (count($data) > 0) {
                $result_fields = [];
                /** @var $field_params \DMF\Core\Model\Field\BaseField */
                foreach ($data as $field_name => $field_params) {
                    $result_fields[] = $field_params->sql($field_name);
                }
                $query = 'ALTER TABLE `' . $this->table_name() . ' MODIFY COLUMN '
                    . implode(', MODIFY COLUMN ', $result_fields) . ';';
                self::$db->query($query);
            }
        }

        /**
         * Возвращает SQL код, необходимый для создания текущей схемы таблицы
         */
        public function generate_sql_for_table()
        {
            $query = 'CREATE TABLE IF NOT EXISTS `' . $this->table_name() . '` (' . PHP_EOL;
            $fields = $this->sql_from_fields();
            $query .= implode(',' . PHP_EOL, $fields) . PHP_EOL . ') ENGINE=' . $this->table_engine
                . ' DEFAULT CHARSET=' . $this->table_encoding;
            return $query;
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
         * Получение данных из БД и выгрузка их в файл фикстур
         */
        public function dump_fixtures()
        {
            $fixture_name = strtolower($this->loaded_module()->name) . '__' . strtolower($this->class_name()) . '.json';
            $data = $this->dump_data();
            $file = new File(DATA_PATH . 'fixtures' . _SEP . $fixture_name);
            $file->open('w+')->block()->write(json_encode($data, JSON_PRETTY_PRINT))->unblock()->close();
        }

        /**
         * Поиск и загрузка данных из фикстур
         * @throws Exception\DBError
         */
        public function load_fixtures()
        {
            // Имя файл фикстуры, генерируется из имени модуля и имени модели
            $fixture_name = strtolower($this->loaded_module()->name) . '__' . strtolower($this->class_name()) . '.json';
            $fixture_path = DATA_PATH . 'fixtures' . _SEP . $fixture_name;
            $fixtures_count = 0;
            // Если файл с фикстурой отсутствует, то ничего не делаем
            if (OS::file_exists($fixture_path)) {
                // Полный путь до файла с фикстурой
                $file = new File($fixture_path);
                $data = $file->open('r+')->read_as_json();
                // Выполнение сохранения объектов из БД в транзакции с откатом при ошибке
                try {
                    self::$db->beginTransaction();
                    // Обходим массив и добавляем каждый элемент в БД
                    foreach ($data as $element) {
                        $this->create($element);
                        $fixtures_count += 1;
                    }
                    self::$db->commit();
                } catch (\Exception $e) {
                    // Откатываемся, если обнаружили ошибку
                    self::$db->rollBack();
                    throw new DBError('[DB] Произошла ошибка при выполнении запроса к БД, транзакция будет отменена.
                        Текст ошибки: ' . $e->getMessage());
                }
            }
            return $fixtures_count;
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
                $fields[] = trim($field_object->sql($field_name));
            }
            return $fields;
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
         * Генерация параметризированного SQL кода
         * Используется для формирования условий при генерации запроса.
         * Возвращает массив из 2 параметров.
         * query: содержит готовый SQL запрос с переменными
         * params: содержит список замен переменных на значения
         * Пример использования:
         *   $this->get_by_condition([
         *     'id'               => 25,
         *     'created_at__gte'  => date('d.m.Y', time()),
         *     '~status__in'      => [1, 2, 5],
         *   ]);
         * Сгенерирует запрос, примерно соответствующий следующему:
         * SELECT * FROM field WHERE id = 25 AND created_at >= '25.12.1935' OR status IN(1, 2, 5)
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
                        case 'eq':
                            $result['queries'][] = $precond . $field_name . '=:' . $field_name;
                            $result['params'][$field_name] = $value;
                            break;
                        /** Проверка на точное различие */
                        case 'neq':
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

        //#############################################################################################################
        //# Методы ORM
        //#############################################################################################################

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
         * @throws RecordDoesNotExists
         */
        public function get_by_pk($pk, $fields = [])
        {
            $sql = sprintf(
                'SELECT %s FROM `%s` WHERE `%s`=%d',
                implode(', ', (count($fields) > 0) ? $fields : $this->fields()),
                $this->table_name(),
                $this->primary_key(),
                (int)$pk
            );
            $data = self::$db->query($sql);
            $data_count = $data->num_rows();
            // Если элемент обнаружен, то добавляем его в сущность
            if ($data_count == 1) {
                $entity = $data->fetch_one();
                $entity_namespace = $this->entity_namespace();
                return new $entity_namespace($this, $entity);
            } elseif ($data_count < 1) {
                // Если запись с указанным id отсутствует,
                // то генерируем соответствующее исключение
                throw new RecordDoesNotExists($this, $pk);
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
            $sql = 'SELECT ' . implode(', ', $select_fields)
                . ' FROM ' . $this->table_name()
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
