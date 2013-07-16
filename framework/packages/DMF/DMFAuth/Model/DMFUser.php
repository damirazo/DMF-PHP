<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\DMFAuth\Model;

    use DMF\Core\Model\Field\BooleanField;
    use DMF\Core\Model\Field\CharField;
    use DMF\Core\Model\Field\DatetimeField;
    use DMF\Core\Model\Field\IntegerField;
    use DMF\Core\Model\Field\PrimaryKeyField;
    use DMF\Core\Model\Model;
    use DMF\Core\Security\Crypt;
    use DMF\Core\Storage\Config;
    use DMF\Core\Storage\Cookie;
    use DMF\Core\Storage\Session;

    /**
     * Class DMFUser
     * Модель пользователя
     * Список дефолтных полей можно расширить,
     * переопределив метод custom_fields для возврата собственного списка полей
     *
     * @package DMF\DMFAuth\Model
     */
    class DMFUser extends Model
    {

        /** @var string Название таблицы (без префикса) */
        public $table_name = 'users';
        /** @var string Имя возвращаемой сущности */
        public $entity_name = 'DMFAuth.DMFUserEntity';


        /**
         * Указание кастомных полей для пользователя
         * Используется, чтобы добавить к базовой модели пользователя дополнительные поля
         * @return array
         */
        public function custom_fields()
        {
            return [];
        }

        /**
         * Указание схемы модели
         * @return array
         */
        public function scheme()
        {
            $fields = [
                'id'           => new PrimaryKeyField(),
                'username'     => new CharField(['length' => 16]),
                'email'        => new CharField(['length' => 64]),
                'password'     => new CharField(['length' => 128]),
                'access_level' => new IntegerField(['length' => 2, 'default' => 0]),
                'auth_token'   => new CharField(['length' => 128]),
                'register_at'  => new DatetimeField(),
                'last_update'  => new DatetimeField(),
                'status'       => new BooleanField()
            ];
            return array_merge($this->custom_fields(), $fields);
        }

        /**
         * Проверка авторизации пользователя
         * @return bool
         */
        public function check_auth()
        {
            // авторизационный токен
            $auth_token = $this->session()->get('auth_token');
            // если авторизационный токен обнаружен, то выполняем подсчет количества пользователей с указанным токеном
            if ($auth_token) {
                $check_user = $this->get_count('*', ['auth_token' => $auth_token]);
                // если число пользователей с указанным токеном равно единице, то возвращаем истину
                if ($check_user == 1) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Сравнение пароля с хэшем
         * @param string $password Пароль
         * @param string $hash     Хэш
         * @return bool
         */
        public function compare_password($password, $hash)
        {
            return !!($hash == $this->create_password_hash($password));
        }

        /**
         * Создание хэша пароля
         * @param string $password Пароль
         * @return string
         */
        public function create_password_hash($password)
        {
            // секретный ключ, указанный в конфигурации
            $secret_key = $this->config('secret_key');
            // создаем соль для хэширования пароля
            $salt = substr(Crypt::hash($secret_key . $password, 'ripemd320'), 7, 16);
            // создаем хэш пароля
            $hash = Crypt::hash($secret_key . $password . $salt, 'sha512');
            return $hash;
        }

        /**
         * Выполнение входа на сайт
         * @param string $username     Имя пользователя
         * @param string $password     Пароль пользователя
         * @param bool   $save_session Сохранять ли авторизацию после закрытия сайта
         * @return bool
         */
        public function login($username, $password, $save_session = false)
        {
            // если число пользователей равно единице,
            // то генерируем новый токен и обновляем дату и время последнего входа
            if ($this->is_exists($username, $password)) {
                // хэш пароля
                $hash = $this->create_password_hash($password);
                // аутентификационный токен
                $auth_token = $this->generate_auth_token();
                // текущие дата и время
                $now = date('Y-m-d H:i:s', time());
                // обновляем пользовательские данные
                $this->update_by_condition(
                    [
                        'auth_token'  => $auth_token,
                        'last_update' => $now
                    ], [
                        'username' => $username,
                        'password' => $hash,
                        'status'   => true
                    ]
                );
                // указываем в сессии новый авторизационный токен
                $this->session()->set('auth_token', $auth_token);
                // если указано, что авторизацию следует запомнить после окончания сессии,
                // то сохраняем авторизационный токен в кукисах, предварительно зашифровав
                if ($save_session) {
                    Cookie::set_crypt('auth_token', $auth_token, null, '/');
                }
                return true;
            }
            return false;
        }

        /**
         * Проверка существования пользователя с указанным именем и паролем
         * @param string $username Имя пользователя
         * @param string $password Пароль
         * @return bool
         */
        public function is_exists($username, $password)
        {
            $users = $this->get_count(
                '*', ['username' => $username, 'password' => $this->create_password_hash($password)]
            );
            return !!($users == 1);
        }

        /**
         * Выполнение выхода с сайта
         */
        public function logout($token)
        {
            // проверяем, что указанный пользователь авторизован
            // и что переданный им токен соответствует хэшу авторизационного токена
            if ($this->session()->get('auth_token')) {
                $auth_token = $this->session()->get('auth_token');
                if ($token == md5($auth_token . Config::get('secret_key'))) {
                    // снимаем авторизацию с пользователя
                    $this->session()->remove('auth_token');
                    Cookie::delete('auth_token');
                    return true;
                }
            }
            return false;
        }

        /**
         * Создание пользователя
         * @param array $data Массив параметров для создания
         */
        public function create($data = [])
        {
            // массив полей
            $fields = $this->scheme();
            $field = [];
            $params = [];
            // основа SQL запроса для вставки нового пользователя в БД
            $sql = 'INSERT INTO `' . $this->table_name() . '` SET ';
            // обходим массив полей
            /** @var $field_object \DMF\Core\Model\Field\BaseField */
            foreach ($fields as $field_name => $field_object) {
                // если в массиве параметров есть значение с данным именем,
                // то добавляем его в массив для добавления в БД
                if (isset($data[$field_name])) {
                    $field[] = $field_name . '=:' . $field_name;
                }
                // если это поле для хранения пароля, то заменяем его на хэш пароля
                if ($field_name == 'password') {
                    $params['password'] = $this->create_password_hash($data['password']);
                } // в противном случае добавляем в массив параметров как есть
                else {
                    if (isset($data[$field_name])) {
                        $params[$field_name] = $data[$field_name];
                    }
                }
            }
            // добавляем в массив для добавления БД и в массив параметров значение авторизационного токена
            $field[] = 'auth_token=:auth_token';
            $params['auth_token'] = $this->generate_auth_token();
            // выполняем запрос добавления нового пользователя в БД
            self::$db->query($sql . implode(', ', $field), $params);
        }

        /**
         * Создание пользователя с использованием ORM
         * @param $data
         */
        public function create_user($data)
        {

        }

        /**
         * Проверка имени пользователя на уникальность
         * @param string $username Имя пользователя
         * @return bool
         */
        public function check_username($username)
        {
            return !!($this->get_count('*', ['username' => $username]) > 0);
        }

        /**
         * Генерация нового аутентификационного токена
         * @return string
         */
        public function generate_auth_token()
        {
            return Crypt::hash(uniqid() . $this->config('secret_key'), 'sha512');
        }

        /**
         * Проверка адреса электронной почты пользователя на уникальность
         * @param string $email Адрес электронной почты пользователя
         * @return bool
         */
        public function check_email($email)
        {
            return !!($this->get_count('*', ['email' => $email]) > 0);
        }

        /**
         * Возвращает объект текущего пользователя или объект гостя
         * @return bool|DMFGuestEntity|mixed
         */
        public function get_user()
        {
            return ($this->authenticate()) ? $this->authenticate() : new DMFGuestEntity($this, []);
        }

        /**
         * Аутентификация пользователя
         * @return bool|mixed
         */
        public function authenticate()
        {
            // авторизационный токен из сессии
            $session_token = $this->session()->get('auth_token');
            // авторизационный токен из кукисов
            $cookie_token = trim(Cookie::get_crypt('auth_token'));
            // проверка наличия токенов
            // токен в сессии имеет приоритет над токеном в кукисах
            if ($session_token) {
                $token = $session_token;
            } elseif ($cookie_token) {
                $token = $cookie_token;
            } else {
                $token = false;
            }
            // если токен задан, то ищем пользователя с таким же токеном
            if ($token) {
                // выборка коллекции сущностей по токену
                /** @var $data \DMF\Core\Model\EntityCollection */
                $data = $this->get_by_condition(
                    [
                        'auth_token' => $token,
                        'status'     => true
                    ], [], 1
                );
                // если количество сущностей равно единице,
                // то считаем ее требуемым пользователем
                if ($data->count() == 1) {
                    $this->session()->set('user', $data->index(0));
                    return $data->index(0);
                }
            }
            return false;
        }


    }
