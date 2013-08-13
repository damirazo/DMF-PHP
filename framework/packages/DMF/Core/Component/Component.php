<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Component;

    use DMF\Core\Application\Application;
    use DMF\Core\Document\Array2XML;
    use DMF\Core\Http\Request;
    use DMF\Core\Http\Response;
    use DMF\Core\Model\Database;
    use DMF\Core\Storage\Config;
    use DMF\Core\Storage\Session;
    use DMF\Core\Template\Template;

    /**
     * Class Component
     * Базовый компонент основных частей фреймворка
     *
     * @package DMF\Core\Component
     */
    class Component
    {

        /** @var \DMF\Core\Model\Database|null Ссылка на соединение с БД */
        public static $db = null;
        /** @var \DMF\Core\Application\Application|null */
        public static $app = null;
        /** @var array Кэш объектов моделей */
        protected static $_models = [];

        /** Конструктор */
        public function __construct()
        {
            // Проверяем, что БД для указанного проекта используются
            if ($this->config('database')['enable']) {
                // Создание подключения к БД
                if (is_null(self::$db)) {
                    self::$db = new Database();
                }
            }
            // Создание экземпляра приложения
            if (is_null(self::$app)) {
                self::$app = Application::get_instance();
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
         * Возвращает объект требуемой модели
         * @param string $name  Имя модели
         * @return \DMF\Core\Model\Model
         */
        public function model($name)
        {
            return self::$app->get_component($name, ComponentTypes::Model);
        }

        /**
         * Возвращает объект формы
         * @param string $name Имя формы
         * @return mixed
         */
        public function form($name)
        {
            return self::$app->get_component($name, ComponentTypes::Form);
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
         *
         * @param mixed $data Данные для представления в JSON
         * @return \DMF\Core\Http\Response
         */
        public function json($data)
        {
            return new Response(json_encode($data), 200, ['Content-type: application/json']);
        }

        /**
         * Возврат данных в виде XML
         *
         * @param mixed $data    Данные для представления в XML
         * @return \DMF\Core\Http\Response
         */
        public function xml($data)
        {
            return new Response($this->array2xml($data), 200, ['Content-type: application/xml']);
        }

        /**
         * Преобразование массива в XML документ
         *
         * @param array  $data      Данные для генерации документа
         * @param string $root_node Имя корневой ноды
         * @param string $encoding  Кодировка документа
         * @param string $version   Версия документа
         * @return string
         */
        protected function array2xml($data, $root_node = 'root', $encoding = 'utf-8', $version = '1.0')
        {
            Array2XML::init($version, $encoding, true);
            $document = Array2XML::createXML($root_node, $data);
            return $document->saveXML();
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
         * Возвращает объект входящего запроса
         * @return \DMF\Core\Http\Request|mixed|null
         */
        public function request()
        {
            return Request::get_instance();
        }

        /**
         * Обработка переменной и удаление лишних символов
         * @param string $value Значение для обработки
         * @return string
         */
        public function clean($value)
        {
            // если получен массив, то пытаемся почистить все его элементы
            if (is_array($value)) {
                $cleaned = [];
                foreach ($value as $element) {
                    $cleaned[] = htmlspecialchars(trim($element));
                }
                return $cleaned;
            }
            // в противном случае чистим лишь полученный элемент
            return htmlspecialchars(trim($value));
        }

        /**
         * Возвращает namespace текущего компонента в виде строки
         *
         * @return string
         */
        public function class_namespace()
        {
            return get_class($this);
        }

        /**
         * Возвращает namespace текущего компонента, разбитый на отдельные части
         *
         * @return array
         */
        public function parsed_namespace()
        {
            $namespace = $this->class_namespace();
            // Проверяем, что у нас используется реальный неймспейс
            if (mb_strpos($namespace, '\\')) {
                list($root_namespace, $module_name, $component_type, $component_name) = explode('\\', $namespace);
                return [
                    'root_namespace' => $root_namespace,
                    'module_name'    => $module_name,
                    'component_type' => $component_type,
                    'component_name' => $component_name,
                ];
            } else {
                // Работа с псевдонеймспейсами пока не поддерживается
                return [];
            }
        }

        /**
         * Возвращает имя класса, в контексте которого вызван
         * @return string
         */
        public function class_name()
        {
            return $this->parsed_namespace()['component_name'];
        }

        /**
         * Возвращает реальный модуль, в пределах которого определен компонент
         * @return \DMF\Core\Module\Module
         */
        public function module()
        {
            return self::$app->get_module_by_name($this->parsed_namespace()['module_name']);
        }

        /**
         * Возвращает объект модуля, в контексте которого выполняется фреймворк в текущий момент
         * @return \DMF\Core\Module\Module|null
         */
        public function loaded_module()
        {
            return self::$app->module;
        }

    }
