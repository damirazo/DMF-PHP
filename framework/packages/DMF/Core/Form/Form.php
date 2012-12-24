<?php

    namespace DMF\Core\Form;

    use DMF\Core\Component\Component;
    use DMF\Core\Http\Request;
    use DMF\Core\Http\Response;
    use DMF\Core\Storage\Config;
    use DMF\Core\Storage\Cookie;

    /**
     * Базовый класс формы
     */
    class Form extends Component
    {

        /** @var bool Активация CSRF защиты */
        public $csrf_protection = false;

        /** @var array Массив ошибок валидации полей формы */
        public $errors = [];

        /** @var string Метод запросов */
        public $method = 'POST';

        /** @var bool|null|string Значение CSRF токена */
        protected $_csrf_token = null;

        /** @var array Массив данных, переданных при инициализации формы */
        protected $_data = [];

        /** @var Кэш списка полей */
        protected $_fields_html = null;

        /** Конструктор формы */
        public function __construct()
        {
            $csrf_token_cookie = $this->config('csrf_token_cookie', 'csrf_token');
            if (Cookie::has($csrf_token_cookie)) {
                $this->_csrf_token = trim(Cookie::get_crypt($csrf_token_cookie));
            } else {
                $token = $this->_generate_csrf_token();
                Cookie::set_crypt($csrf_token_cookie, $token, null, '/');
                $this->_csrf_token = $token;
            }
        }

        /**
         * Схема формы
         * @return array
         */
        public function scheme()
        {
            return [];
        }

        /**
         * Возвращает массив с именами полей схемы формы
         * @return array
         */
        protected function _field_names()
        {
            return array_keys($this->scheme());
        }

        /**
         * Генерация массива с полями формы
         * @return array
         */
        protected function _get_html_fields()
        {
            if (is_null($this->_fields_html)) {
                $fields = [];
                /** @var $element_object \DMF\Core\Form\Field\BaseField */
                foreach ($this->scheme() as $element_name => $element_object) {
                    /** Приоритет передачи значения полю */
                    // Если имеется уже переданное запросом значение в поле
                    if ($element_object->get($element_name)) {
                        $default = $element_object->get($element_name);
                    } // Если в поле были переданы данные методом bound
                    elseif (isset($this->_data[$element_name])) {
                        $default = $this->_data[$element_name];
                    } // В противном случае считаем поле пустым
                    else {
                        $default = '';
                    }
                    /** Обработка передаваемого в форму значения */
                    if (is_array($default)) {
                        $cleaned_values = [];
                        foreach ($default as $value) {
                            $cleaned_values[] = $this->clean($value);
                        }
                        $default = $cleaned_values;
                    } else {
                        $default = $this->clean($default);
                    }
                    /** Формирование массива с данными о поле */
                    $fields[$element_name] = [
                        'field' => $element_object->_get_html_code($element_name, $default),
                        'label' => $element_object->label,
                        'error' => ($this->errors($element_name)) ? $this->errors($element_name)->error() : null
                    ];
                }
                $this->_fields_html = $fields;
            } else {
                $fields = $this->_fields_html;
            }

            return $fields;
        }

        /**
         * Возвращает данные одного поля формы
         * @param mixed $name Имя поля
         *
         * @return array
         */
        public function field($name)
        {
            $fields = $this->_get_html_fields();
            if (isset($fields[$name])) {
                return $fields[$name];
            }

            return false;
        }

        /**
         * Возвращает массив из всех полей формы
         * @return array
         */
        public function fields()
        {
            return $this->_get_html_fields();
        }

        /**
         * Возвращает данные исходя из метода запроса
         *
         * @return array
         */
        protected function get_data_from_method()
        {
            switch ($this->method) {
                case 'POST':
                    $method = $_POST;
                    break;
                case 'GET':
                    $method = $_GET;
                    break;
                default:
                    $method = $_POST;
                    break;
            }
            return $method;
        }

        /**
         * Проверка валидности полей формы
         *
         * @return bool
         */
        public function validate()
        {
            $errors = [];
            $csrf_token = $this->get_data_from_method('csrf_token');
            /** Проверка CSRF токена */
            if ($this->csrf_protection) {
                if (!$csrf_token) {
                    return new Response('Отсутствует значение CSRF токена!', 403);
                } else {
                    if (!$this->_check_csrf_token($csrf_token)) {
                        return new Response('Получен неверный CSRF токен!', 403);
                    }
                }
            }
            /** Валидация полей формы */
            $fields = $this->scheme();
            /** @var $field_object \DMF\Core\Form\Field\BaseField */
            foreach ($fields as $field_name => $field_object) {
                $value = ($this->get($field_name)) ? $this->get($field_name) : '';
                /** @var $validator \DMF\Core\Form\Validator */
                $validator = $field_object->validate($this, $value, $field_object->label);
                if (!$validator->is_valid()) {
                    $errors[$field_name] = $validator;
                }
            }
            $this->errors = $errors;

            return !!(count($errors) == 0);
        }

        /**
         * Возвращает массив ошибок для требуемого поля
         * @param string $name Имя поля
         *
         * @return \DMF\Core\Form\Validator
         */
        public function errors($name)
        {
            if (isset($this->errors[$name])) {
                return $this->errors[$name];
            }

            return false;
        }

        /**
         * Возвращает массив с очищенными данными, введенными в форму
         * @return array
         */
        public function cleaned_data()
        {
            $data = $this->data();
            $result = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $r = [];
                    foreach ($value as $element) {
                        $r[] = $this->clean($element);
                    }
                    $result[$key] = $r;
                } else {
                    $result[$key] = $this->clean($value);
                }
            }

            return $result;
        }

        /**
         * Возвращает массив с сырыми данными, введенными в форму
         * @return array
         */
        public function data()
        {
            $result = [];
            $data = $this->get_data_from_method();
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $r = [];
                    foreach ($value as $element) {
                        $r[] = $element;
                    }
                    $result[$key] = $r;
                } else {
                    $result[$key] = $value;
                }
            }

            return $result;
        }

        /**
         * Получение очищенного значения, переданного в форму или значения по умолчанию
         * @param string $name    Имя значения
         * @param bool   $default Значение по умолчанию
         *
         * @return bool
         */
        public function get($name, $default = false)
        {
            if (isset($this->cleaned_data()[$name])) {
                return $this->cleaned_data()[$name];
            }

            return $default;
        }

        /**
         * Получение сырого значения, переданного в форму или значения по умолчанию
         * @param string $name    Имя значения
         * @param bool   $default Значение по умолчанию
         *
         * @return bool
         */
        public function raw($name, $default = false)
        {
            if (isset($this->data()[$name])) {
                return $this->data()[$name];
            }

            return $default;
        }

        /**
         * Привязывает начальные данные к форме
         * @param array $data Данные для формы
         */
        public function bound($data)
        {
            $this->_data = $data;
        }

        /**
         * Генерация CSRF токена
         *
         * @return string
         */
        public function _generate_csrf_token()
        {
            $token = substr(md5(uniqid() . time() . $this->config('secret_key', uniqid())), 5, 15);

            return $token;
        }

        /**
         * Получение текущего CSRF токена
         *
         * @return bool|null|string
         */
        public function _get_csrf_token()
        {
            return $this->_csrf_token;
        }

        /**
         * Возвращает поле CSRF токена
         *
         * @return string
         */
        public function csrf_token()
        {
            return $this->csrf_protection
                ? '<div style="display:none;"><input type="hidden" name="csrf_token" value="'
                    . trim($this->_csrf_token) . '"></div>'
                : '';
        }

        /**
         * Проверка валидности полученного CSRF токена
         *
         * @param string $token CSRF токен
         *
         * @return bool
         */
        public function _check_csrf_token($token)
        {
            return !!($token == $this->_csrf_token);
        }

        /**
         * Проверяет были ли получены данные для формы
         * @return bool
         */
        public function is_received()
        {
            return !!($this->request()->get_method() == $this->method);
        }

    }
