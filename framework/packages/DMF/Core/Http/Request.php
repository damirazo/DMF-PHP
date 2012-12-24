<?php

    namespace DMF\Core\Http;

    /**
     * Класс для работы с входящими запросами
     */
    class Request
    {

        /** @var null|Request Инстанс объекта */
        private static $_instance = null;

        /** Запрет на создание объекта */
        private function __construct()
        {
        }

        /** Запрет на копирование объекта */
        private function __clone()
        {
        }

        /**
         * Возвращает инстанс объекта
         * @return Request|null
         */
        public static function get_instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new Request();
            }
            return self::$_instance;
        }

        /**
         * Базовый URI сайта
         * @return string
         */
        public function base_url()
        {
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
            return $protocol . '://' . $_SERVER['HTTP_HOST'];
        }

        /**
         * Строка запроса
         * @return string
         */
        public function request_uri()
        {
            // Если URI отсутствует, то указываем его как корень сайта
            if (!isset($_SERVER['PATH_INFO'])) {
                $_SERVER['PATH_INFO'] = '/';
            }
            // Разбиваем URI на сегменты
            $segments = explode('/', $_SERVER['PATH_INFO']);
            $new_segments = [];
            // Обходим массив сегментов
            foreach ($segments as $segment) {
                // Если сегмент не пустой, то добавляем его в общий массив сегментов URi
                if (!is_null($segment) && $segment != '') {
                    $new_segments[] = $segment;
                }
            }
            // Если сегменты отсутствуют, то считаем текущий URI корнем сайта
            if (count($new_segments) < 1) {
                return '/';
            }
            // В противном случае собираем сегменты в строку и возвращаем
            else {
                $uri = implode('/', $new_segments);
                return '/' . $uri . '/';
            }
        }

        /**
         * Получение клиентского IP адреса
         * @return bool|string
         */
        public function client_ip()
        {
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            }
            elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            else {
                $ip = '0.0.0.0';
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
            return false;
        }

        /**
         * Возвращение значения из $_POST массива
         * @param string     $name    Имя значения
         * @param bool|mixed $default Значение по умолчанию
         * @return bool
         */
        public function _post($name, $default = false)
        {
            if (isset($_POST[$name])) {
                return $_POST[$name];
            }
            return $default;
        }

        /**
         * Возвращение значения из $_GET массива
         * @param string     $name    Имя значения
         * @param bool|mixed $default Значение по умолчанию
         * @return bool
         */
        public function _get($name, $default = false)
        {
            if (isset($_GET[$name])) {
                return $_GET[$name];
            }
            return $default;
        }

        /**
         * Возвращение значения из $_REQUEST массива
         * @param string     $name    Имя значения
         * @param bool|mixed $default Значение по умолчанию
         * @return bool
         */
        public function _request($name, $default = false)
        {
            if (isset($_REQUEST[$name])) {
                return $_REQUEST[$name];
            }
            return $default;
        }

    }
