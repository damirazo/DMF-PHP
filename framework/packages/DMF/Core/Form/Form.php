<?php

    namespace DMF\Core\Form;

    use DMF\Core\Application\Application;
    use DMF\Core\Http\Exception\HttpError;
    use DMF\Core\Component\Component;
    use DMF\Core\Storage\Cookie;

    /**
     * Базовый класс для форм
     */
    class Form extends Component
    {

        /** @var bool Активация CSRF защиты */
        public $csrf = true;

        /** @var string Имя метода получения запроса формы */
        public $method = 'POST';

        /** @var null|string CSRF токен */
        protected $csrf_token = null;

        /** @var array|null Список кэшированных объектов полей */
        protected $fields_cache = null;

        /** @var array Типы полей и сопоставленные им классы */
        protected $field_types = [];

        /** @var array Предварительно заполненные поля формы */
        protected $bounded_data = [];

        /** @var bool Была ли заполнена форма */
        protected $is_bound = false;

        /**
         * Конструктор формы
         */
        public function __construct()
        {
            // проверяем, активна ли CSRF защита
            if ($this->csrf) {
                // если значение CSRF токена равно нулю, то ищем его в сессии и кукисах
                // в противном случае генерируем заново
                if (is_null($this->csrf_token)) {
                    if ($this->session()->get('csrf_token')) {
                        $this->csrf_token = $this->session()->get('csrf_token');
                    }
                    elseif (Cookie::get_crypt('csrf_token')) {
                        $this->csrf_token = Cookie::get_crypt('csrf_token');
                    }
                    else {
                        $csrf_token = $this->generate_csrf_token();
                        $this->session()->set('csrf_token', $csrf_token);
                        Cookie::set_crypt(
                            'csrf_token',
                            $csrf_token,
                            null,
                            null,
                            null,
                            null,
                            true
                        );
                        $this->csrf_token = $csrf_token;
                    }
                }
            }
        }

        /**
         * Определение схемы формы
         * @return array
         */
        public function scheme()
        {
            return [];
        }

        /**
         * Возвращает массив полей формы
         * @return array
         */
        public function fields()
        {
            // проверяем наличие полей в кэше
            // если кэш полей пуст, то генерируем список полей
            if (is_null($this->fields_cache)) {
                $fields = [];
                // обходим схему формы
                foreach ($this->scheme() as $field_name => $field_data) {
                    // получаем имя класса для указанного типа поля
                    $field_class = $this->get_class_namespace_for_field_type($field_data['type']);
                    // создаем экземпляр класса поля и помещаем его в массив
                    /** @var $field \DMF\Core\Form\Field\BaseField */
                    $field = new $field_class($this, $field_name, $field_data);
                    $fields[$field_name] = $field;
                }
                // сохраняем сгенерированный массив полей в кэш
                $this->fields_cache = $fields;
                return $fields;
            }
            else {
                return $this->fields_cache;
            }
        }

        /**
         * Возвращает значение поля с именем $name
         * @param $name
         * @return null
         */
        public function field($name)
        {
            if (isset($this->fields()[$name])) {
                return $this->fields()[$name];
            }
            return null;
        }

        /**
         * Список ошибок от полей формы
         * @return array
         */
        public function errors()
        {
            $errors = [];
            /** @var $field_object \DMF\Core\Form\Field\BaseField */
            foreach ($this->fields() as $field_name => $field_object) {
                $errors[$field_name] = $field_object->error();
            }
            return $errors;
        }

        /**
         * Возвращает информацию о том валидна ли форма
         * @return bool
         */
        public function is_valid()
        {
            // если форма не была получена, то следовательно она не валидна
            if ($this->is_received()) {
                // проверяем корректность полученного CSRF токена
                if ($this->check_csrf_token()) {
                    $is_valid = true;
                    // обходим массив полей
                    /** @var $field_object \DMF\Core\Form\Field\BaseField */
                    foreach ($this->fields() as $field_name => $field_object) {
                        // валидируем поле и получаем объект валидатора
                        $validator = $field_object->validate();
                        // если валидатор невалиден, то ставим значение формы невалидным
                        if ($validator->is_valid() === false) {
                            $is_valid = false;
                        }
                    }
                    return $is_valid;
                }
            }
            return false;
        }

        /**
         * Возвращает информацию о том была ли получена форма
         * @return bool
         */
        public function is_received()
        {
            return !!($this->request()->get_method() == $this->method);
        }

        /**
         * Возвращает значение CSRF токена
         * @return null|string
         */
        public function csrf_token()
        {
            return $this->csrf_token;
        }

        /**
         * Регистрация класса формы для требуемого типа
         * @param string $name      Имя типа
         * @param string $namespace Пространство имен требуемого класса
         */
        public function register_field_type($name, $namespace)
        {
            $this->field_types[$name] = $namespace;
        }

        /**
         * Возвращает массив данных, переданных форме
         * @return array
         */
        public function data()
        {
            $data = [];
            $method_var = $this->get_form_data_container();
            foreach ($this->fields() as $field_name => $field_object) {
                if (isset($method_var[$field_name])) {
                    $data[$field_name] = $method_var[$field_name];
                }
            }
            return $data;
        }

        /**
         * Возвращает предварительно отфильтрованный массив данных, переданный форме
         * @return array
         */
        public function cleaned_data()
        {
            $data = [];
            $method_var = $this->get_form_data_container();
            foreach ($this->fields() as $field_name => $field_object) {
                if (isset($method_var[$field_name])) {
                    $data[$field_name] = $this->clean($method_var[$field_name]);
                }
            }
            return $data;
        }

        /**
         * Возвращает значение поля с требуемым именем
         * @param string $field_name Имя поля
         * @param mixed  $default    Значение по умолчанию
         * @return bool|mixed
         */
        public function value($field_name, $default = false)
        {
            $method_var = $this->get_form_data_container();
            // проверяем наличие переменной с нужным именем в глобальных массивах
            if (isset($method_var[$field_name])) {
                return $method_var[$field_name];
            }
            // если переменная не была обнаружена, то ищем ее в переданных в форму данных
            elseif (isset($this->bounded_data[$field_name])) {
                return $this->bounded_data[$field_name];
            }
            return $default;
        }

        /**
         * Возвращает зачищенное значение поля с требуемым именем
         * @param string $field_name Имя поля
         * @param mixed  $default    Значение по умолчанию
         * @return bool|mixed
         */
        public function cleaned_value($field_name, $default=false) {
            if (isset($this->cleaned_data()[$field_name])) {
                return $this->cleaned_data()[$field_name];
            }
            elseif (isset($this->bounded_data[$field_name])) {
                return $this->clean($this->bounded_data[$field_name]);
            }
            return $default;
        }

        /**
         * Возвращает переменную, содержащую переданные форме значения
         * @return array
         */
        public function get_form_data_container()
        {
            switch ($this->request()->get_method()) {
                case 'POST':
                    return $_POST;
                case 'GET':
                    return $_GET;
                default:
                    return $_REQUEST;
            }
        }

        /**
         * Заполнение формы значениями
         * @param array $data Массив значений
         */
        public function bound($data)
        {
            $this->bounded_data = $data;
            $this->is_bound = true;
        }

        /**
         * Возвращает информацию о том, была ли заполнена форма
         * @return bool
         */
        public function is_bound()
        {
            return $this->is_bound;
        }

        /**
         * Возвращает имя формы
         * @return string
         */
        public function name()
        {
            return strtolower($this->get_class_name());
        }

        /**
         * Поле с CSRF токеном
         * @return string
         */
        public function csrf_field()
        {
            return '<input type="hidden" name="csrf_token" value="'.$this->csrf_token.'">';
        }

        /**
         * Возвращает строку с пространством имен для требуемого типа поля
         * @param string $type Тип поля
         * @return string
         * @description Собственные поля должны находится в папке Form/Field требуемого модуля,
         * в противном случае их загрузка будет невозможна
         */
        protected function get_class_namespace_for_field_type($type)
        {
            // разбиваем строку по разделителю точки
            $segments = explode('.', $type);
            // если число сегментов равно двум, значит указаны имя модуля и имя поля
            if (count($segments) == 2) {
                // если имя модуля DMF, следовательно это псевдоним системного модуля
                if ($segments[0] == 'DMF') {
                    $namespace = '\DMF\Core\Form\Field\\' . $segments[1];
                }
                // в противном случае возвращаем объект требуемого модуля и достаем его пространство имен
                else {
                    $module = Application::get_instance()->get_module_by_name($segments[0]);
                    $namespace = $module->namespace . '\Form\Field\\' . $segments[1];
                }
            }
            // в противном случае считаем, что поле находится в данном модуле
            else {
                $module = $this->get_module();
                $namespace = $module->namespace . '\Form\Field\\' . $segments[0];
            }
            return $namespace;
        }

        /**
         * Проверка CSRF токена при активированной защите
         * @throws \DMF\Core\Http\Exception\HttpError
         * @return bool
         */
        protected function check_csrf_token()
        {
            if ($this->csrf) {
                $received_csrf_token = $this->value('csrf_token');
                if (!$received_csrf_token) {
                    throw new HttpError('Отсутствует значение CSRF токена!', 403);
                }
                if (!$this->compare_csrf_token($received_csrf_token)) {
                    throw new HttpError('Получено неверное значение CSRF токена!', 403);
                }
            }
            return true;
        }

        /**
         * Проверка валидности полученного CSRF токена
         * @param string $csrf_token CSRF токен
         * @return bool
         */
        protected function compare_csrf_token($csrf_token)
        {
            return !!($this->csrf_token == $csrf_token);
        }

        /**
         * Возвращает новый CSRF токен
         * @return string
         */
        protected function generate_csrf_token()
        {
            return substr(md5(uniqid() . $this->config('secret_key')), 0, 16);
        }

    }
