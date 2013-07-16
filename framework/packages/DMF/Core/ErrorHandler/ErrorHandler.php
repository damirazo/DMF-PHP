<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\ErrorHandler;

    use DMF\Core\Http\Request;
    use DMF\Core\OS\OS;
    use DMF\Core\Storage\Config;
    use Exception;

    /**
     * Class ErrorHandler
     * Перехват ошибок и логгирование
     *
     * @package DMF\Core\ErrorHandler
     */
    class ErrorHandler
    {

        /** @var array Массив коллбеков, выполняемых после получения ошибки */
        private static $_callbacks = [];

        /**
         * Регистрация коллбеков
         * @param array $callbacks Массив функций
         */
        // TODO: Логика коллбеков не реализована
        public static function register_callbacks($callbacks = [])
        {
            self::$_callbacks = $callbacks;
        }

        /**
         * Перехват исключений
         * @param \Exception|\DMF\Core\Http\Exception\HttpError $exception
         */
        public static function exception_handler($exception)
        {
            $http_code = ($exception instanceof \DMF\Core\Http\Exception\HttpError) ? $exception->get_http_code() : 500;
            self::show_error(
                $exception->getMessage(), 'EXCEPTION', $exception->getFile(), $exception->getLine(), $http_code
            );
        }

        /**
         * Вывод информации о перехваченной ошибке
         * @param string $message   Текст ошибки
         * @param string $type      Тип ошибки
         * @param string $file      Название файла
         * @param string $line      Строка
         * @param int    $http_code Код HTTP состояния
         */
        public static function show_error($message, $type, $file, $line, $http_code = 500)
        {
            // очищаем содержимое всех буфферов вывода
            while (ob_get_level()) {
                ob_end_clean();
            }
            // отправляем HTTP код состояния
            http_response_code($http_code);
            // формируем путь до файла, сгенерировавшего ошибку
            $error_file = str_replace(PROJECT_PATH, '{PROJECT_PATH}' . _SEP, $file);
            $error_templates_path = CORE_PATH . 'ErrorHandler' . _SEP . 'templates';

            // Даем доступ к информации об ошибке лишь определенным ip адресам
            if (self::is_debug()) {
                // формируем стек вызовов
                $error_stack = self::backtrace(debug_backtrace());
                // контейнер для информации об ошибке
                $error = [
                    'message' => $message,
                    'path'    => $error_file,
                    'line'    => $line,
                    'stack'   => $error_stack,
                    'code'    => $http_code,
                    'type'    => $type
                ];
                // импортируем шаблон для отображения страницы ошибки
                require_once OS::join($error_templates_path, 'error_debug.php');
            } else {
                $error = [
                    'message' => $message,
                    'code'    => $http_code
                ];
                require_once OS::join($error_templates_path, 'error.php');
            }

            // записываем в лог информацию об ошибке
            self::log($message, $type, $error_file, $line, $http_code);
            exit();
        }

        /**
         * Проверка наличия доступа к дебаговой информации
         * @return bool
         */
        public static function is_debug()
        {
            $debug = Config::has('debug') ? Config::get('debug') : DEBUG;
            if ($debug) {
                if (Config::has('allowed_ips')) {
                    $allowed_ips = Config::get('allowed_ips');
                    $client_ip = Request::get_instance()->client_ip();
                    if (in_array($client_ip, $allowed_ips)) {
                        return true;
                    }
                }
            }
            return false;
        }

        /**
         * Создание стека вызова
         * @param array $trace Стек
         * @return array
         */
        protected static function backtrace($trace)
        {
            // массив для хранения стека вызовов
            $backtrace = [];
            // инвертируем ключи стека и обходим его
            foreach (array_reverse($trace) as $data) {
                $args = [];
                // если указанный элемент имеет список аргументов, то обходим их
                if (isset($data['args']) && is_array($data['args']) && count($data['args']) > 0) {
                    $arguments = $data['args'];
                    // обходим список аргументов
                    foreach ($arguments as $argument) {
                        // если данный аргумент является экземпляром объекта исключения,
                        // то вызываем рекурсивно данную функцию и достаем стек вызовов данного исключения
                        if ($argument instanceof \Exception) {
                            $bt = self::backtrace($argument->getTrace());
                            foreach ($bt as $b) {
                                $backtrace[] = $b;
                            }
                        }
                    }
                }
                // формируем элемент стека вызова
                $backtrace[] = [
                    // путь до файла
                    'file'     => isset($data['file'])
                            ? str_replace(PROJECT_PATH, '{PROJECT_PATH}' . _SEP, $data['file'])
                            : '-',
                    // строка в файле
                    'line'     => isset($data['line']) ? $data['line'] : '-',
                    // имя класса
                    'class'    => isset($data['class']) ? $data['class'] : '-',
                    // имя метода/функции
                    'function' => isset($data['function']) ? $data['function'] : '-',
                    // массив аргументов
                    'args'     => $args
                ];
            }
            return $backtrace;
        }

        /**
         * Запись сообщения об ошибке в лог
         * @param string $message   Текст ошибки
         * @param string $type      Тип ошибки
         * @param string $file      Путь до файла
         * @param int    $line      Строка, на которой произошла ошибка
         * @param int    $http_code Код HTTP
         * @param null   $stack     Стек вызовов
         */
        protected static function log($message, $type, $file, $line, $http_code, $stack = null)
        {
            $log_file_name = PROJECT_PATH . 'logs' . _SEP . date('Y-m-d', time()) . '.log';
            $log_file = fopen($log_file_name, 'a');
            $log_string
                    = '[' . date('Y-m-d H:i:s') . '], ' . $http_code . ', ' . (($_SERVER['REMOTE_ADDR'])
                            ? $_SERVER['REMOTE_ADDR']
                            : $_SERVER['X_FORWARDED_FOR']) . ', ' . $type
                    . ', ' . $file . ':' . $line . ', "'
                    . trim($message)
                    . '"' . PHP_EOL;
            flock($log_file, LOCK_EX);
            fwrite($log_file, $log_string);
            flock($log_file, LOCK_UN);
            fclose($log_file);
        }

        /**
         * Перехват пользовательских ошибок
         * @param int    $num     Номер ошибки
         * @param string $message Текст ошибки
         * @param string $file    Название файла
         * @param string $line    Номер строки
         * @param array  $context Контекст
         */
        public static function user_error_handler($num, $message, $file, $line, $context)
        {
            self::show_error($message, 'USER', $file, $line, 500);
        }

        /**
         * Перехват фатальных ошибок
         */
        public static function fatal_error_handler()
        {
            $error = error_get_last();
            if ($error['type'] == E_ERROR || $error['type'] == E_COMPILE_ERROR || $error['type'] == E_CORE_ERROR) {
                self::show_error($error['message'], 'FATAL', $error['file'], $error['line'], 500);
            }
        }

        /**
         * Активация перехвата ошибок
         */
        public static function run()
        {
            register_shutdown_function(__CLASS__ . '::fatal_error_handler');
            set_exception_handler(__CLASS__ . '::exception_handler');
            set_error_handler(__CLASS__ . '::user_error_handler');
        }

    }
