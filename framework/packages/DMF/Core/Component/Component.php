<?php

    namespace DMF\Core\Component;

    use DMF\Core\Application\Application;
    use DMF\Core\Model\Database;
    use DMF\Core\Storage\Config;
    use DMF\Core\Storage\Session;
    use DMF\Core\Http\Request;
    use DMF\Core\Http\Response;
    use DMF\Core\Template\Template;

    /**
     * Базовый класс для большинства частей фреймворка
     */
    class Component
    {

        /** @var \DMF\Core\Model\Database|null Ссылка на соединение с БД */
        public static $db = null;

        /** @var array Кэш объектов моделей */
        protected static $_models = [];

        /** Конструктор */
        public function __construct()
        {
            /** Проверяем наличие соединения с БД */
            if (is_null(self::$db)) {
                self::$db = new Database();
            }
        }

        /**
         * Возвращает значение настройки
         * @param string $name    Имя настройки
         * @param mixed  $default Значение по умолчанию
         * @return bool|mixed
         */
        public function config($name, $default = false)
        {
            return Config::get($name, $default);
        }

        /**
         * Возвращает объект требуемого компонента
         * @param string $name Имя компонента
         * @param string $type Тип компонента (Form, Model)
         * @return mixed
         */
        protected function get_component($name, $type)
        {
            $data = explode('.', $name);
            // Если указан лишь один параметр в строке, то считаем его именем конмонента
            if (count($data) < 2) {
                // Модуль считаем текущим
                $module = $this->get_module();
                $component_name = $data[0];
            }
            // Если указаны оба параметра, то следовательно они являются именем модуля и компонента
            else {
                // Получаем модуль по его имени
                $module = Application::get_instance()->get_module_by_name($data[0]);
                $component_name = $data[1];
            }
            $component_namespace = $module->namespace . '\\' . $type . '\\' . $component_name;
            return new $component_namespace();
        }

        /**
         * Возвращает объект требуемой модели
         * @param string $name  Имя модели
         * @return mixed
         */
        public function model($name)
        {
            return $this->get_component($name, 'Model');
        }

        /**
         * Возвращает объект формы
         * @param string $name Имя формы
         * @return mixed
         */
        public function form($name)
        {
            return $this->get_component($name, 'Form');
        }

        /**
         * Возвращает объект сессии
         * @return \DMF\Core\Storage\Session|null
         */
        public function session()
        {
            return Session::get_instance();
        }

        /**
         * Возвращает объект входящего запроса
         * @return \DMF\Core\Http\Request|mixed|null
         */
        public function request()
        {
            return Request::get_instance();
        }

        /**
         * Рендер шаблона
         * @param string $template_name      Имя шаблона
         * @param array  $data               Данные, передаваемые в шаблон
         * @param int    $http_response_code Код HTTP состояния
         * @return \DMF\Core\Http\Response
         */
        public function render($template_name, $data = [], $http_response_code = 200)
        {
            return new Template($template_name, $data, $http_response_code);
        }

        /**
         * Вывод строки с текстом
         * @param string $message          Текст сообщения
         * @param int    $http_status_code HTTP код ошибки
         * @param array  $headers          Список HTTP заголовков
         * @return \DMF\Core\Http\Response
         */
        public function string($message, $http_status_code = 200, $headers = [])
        {
            return new Response($message, $http_status_code, $headers);
        }

        /**
         * Возврат данных в виде JSON объекта
         * @param mixed $data Данные для представления в JSON
         * @return \DMF\Core\Http\Response
         */
        public function json($data)
        {
            return new Response(json_encode($data), 200, ['Content-type: application/json']);
        }

        /**
         * Вывод дампа объекта
         * @param mixed $data Данные для вывода
         * @return \DMF\Core\Http\Response
         */
        public function dump($data)
        {
            return new Response('<pre>' . print_r($data, true) . '</pre>');
        }

        /**
         * Редиректит на страницу $path
         * @param string $path URI страницы (без доменного имени)
         * @return bool
         */
        public function redirect($path)
        {
            header('Location: ' . $this->request()->base_url() . $path);
            return true;
        }

        /**
         * Обработка переменной и удаление лишних символов
         * @param string $value Значение для обработки
         * @return string
         */
        public function clean($value)
        {
            return htmlspecialchars(trim($value));
        }

        /**
         * Возвращает объект текущего модуля
         * @return \DMF\Core\Module\Module|null
         */
        protected function get_module()
        {
            return Application::get_instance()->module;
        }

        /**
         * Возвращает имя модуля, в котором инициализирован дочерний класс
         * @return mixed
         */
        protected function get_module_name()
        {
            return $this->get_module()->name;
        }

        /**
         * Возвращает имя класса, в контексте которого вызван
         * @return string
         */
        protected function get_class_name()
        {
            $namespace = get_class($this);
            $pieces = (mb_strpos($namespace, '\\')) ? explode('\\', $namespace) : explode('_', $namespace);
            return $pieces[count($pieces) - 1];
        }

    }
