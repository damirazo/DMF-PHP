<?php

    namespace DMF\Auth\Model;

    use DMF\Core\Model\Model;
    use DMF\Core\Model\Field\PrimaryKeyField;
    use DMF\Core\Model\Field\CharField;
    use DMF\Core\Model\Field\IntegerField;
    use DMF\Core\Model\Field\BooleanField;
    use DMF\Core\Model\Field\DatetimeField;
    use DMF\Core\Storage\Session;
    use DMF\Core\Security\Crypt;
    use DMF\Core\Storage\Config;
    use DMF\Core\Storage\Cookie;

    /**
     * Базовая модель пользователя
     */
    class DMFUser extends Model
    {

        /**
         * Указание кастомных полей для пользователя
         * @return array
         */
        public function _custom_fields()
        {
            return [];
        }

        /**
         * Указание схемы модели
         * @return array
         */
        public function _scheme()
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
            return array_merge($this->_custom_fields(), $fields);
        }

        /**
         * Проверка авторизации пользователя
         * @return bool
         */
        public function check_auth()
        {
            $auth_token = $this->session()->get('auth_token');
            if ($auth_token) {
                $user = self::$db->query(
                    'SELECT COUNT(*) FROM ' . $this->_get_table_name() . ' WHERE auth_token=:auth_token LIMIT 1',
                    ['auth_token' => $auth_token]
                );
                if ($user->num_rows() == 1) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Создание хэша пароля
         * @param string $password Пароль
         * @return string
         */
        public function create_password_hash($password)
        {
            $secret_key = $this->config('secret_key');
            $salt = substr(Crypt::hash($secret_key . $password, 'ripemd320'), 7, 16);
            $hash = Crypt::hash($secret_key . $password . $salt, 'sha512');
            return $hash;
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
         * Создание пользователя
         * @param array $data Массив параметров для создания
         */
        public function create_user($data = [])
        {
            $fields = $this->_scheme();
            $field = [];
            $params = [];
            $sql = 'INSERT INTO `' . $this->_get_table_name() . '` SET ';
            /** @var $field_object \DMF\Core\Model\Field\BaseField */
            foreach ($fields as $field_name => $field_object) {
                if (isset($data[$field_name])) {
                    $field[] = $field_name . '=:' . $field_name;
                }
                if ($field_name == 'password') {
                    $params['password'] = $this->create_password_hash($data['password']);
                }
                else {
                    if (isset($data[$field_name])) {
                        $params[$field_name] = $data[$field_name];
                    }
                }
            }
            $field[] = 'auth_token=:auth_token';
            $params['auth_token'] = $this->generate_auth_token();
            self::$db->query($sql . implode(', ', $field), $params)->send();
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
            }
            elseif ($cookie_token) {
                $token = $cookie_token;
            }
            else {
                $token = false;
            }
            // если токен задан, то ищем пользователя с таким же токеном
            if ($token) {
                // выборка коллекции сущностей по токену
                $data = $this->get_by_condition(
                    [
                        'auth_token' => $token,
                        'status'     => true
                    ], [], 1
                );
                // если количество сущностей равно единице,
                // то считаем ее требуемым пользователем
                if ($data->count() == 1) {
                    $this->session()->set('user', $data[0]);
                    return $data;
                }
            }
            return false;
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
            $hash = $this->create_password_hash($password);
            $check_user = self::$db->query(
                'SELECT id, auth_token FROM `' . $this->_get_table_name()
                        . '` WHERE username=:username AND password=:password AND status=1 LIMIT 1',
                ['username' => $username, 'password' => $hash]
            )->fetch_one();
            if (count($check_user) == 1) {
                $auth_token = $this->generate_auth_token();
                self::$db->query(
                    'UPDATE `' . $this->_get_table_name()
                            . '` SET auth_token=:auth_token, last_update=NOW()
                        WHERE username=:username AND password=:password AND status=1',
                    ['auth_token' => $auth_token, 'username' => $username, 'password' => $hash]
                )->send();
                $this->session()->set('auth_token', $auth_token);
                if ($save_session) {
                    Cookie::set_crypt('auth_token', $auth_token, null, '/');
                }
                return true;
            }
            return false;
        }

        /**
         * Выполнение выхода с сайта
         */
        public function logout($token)
        {
        }

        /**
         * Проверка имени пользователя на уникальность
         * @param string $username Имя пользователя
         * @return bool
         */
        public function check_username($username)
        {
            $check_username = self::$db->query(
                'SELECT * FROM `'
                        . $this->_get_table_name() . '` WHERE username=:username LIMIT 1',
                ['username' => $username]
            );
            return !!($check_username->num_rows() == 1);
        }

        /**
         * Проверка адреса электронной почты пользователя на уникальность
         * @param string $email Адрес электронной почты пользователя
         * @return bool
         */
        public function check_email($email)
        {
            $check_email = self::$db->query(
                'SELECT * FROM `'
                        . $this->_get_table_name() . '` WHERE email=:email LIMIT 1',
                ['email' => $email]
            );
            return !!($check_email->num_rows() == 1);
        }

    }
