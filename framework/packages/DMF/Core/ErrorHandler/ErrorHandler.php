<?php

    namespace DMF\Core\ErrorHandler;

    use Exception;

    /**
     * Перехват и вывод ошибок
     */
    class ErrorHandler
    {

        /** @var array Массив коллбеков, выполняемых после получения ошибки */
        private static $_callbacks = [];

        /**
         * Регистрация коллбеков
         * @param array $callbacks Массив функций
         */
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
            self::show_error($exception->getMessage(), $exception->getFile(), $exception->getLine(), $http_code);
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
            self::show_error($message, $file, $line, 500);
        }

        /**
         * Перехват фатальных ошибок
         */
        public static function fatal_error_handler()
        {
            $error = error_get_last();
            if ($error['type'] == E_ERROR || $error['type'] == E_COMPILE_ERROR || $error['type'] == E_CORE_ERROR) {
                self::show_error($error['message'], $error['file'], $error['line'], 500);
            }
        }

        /**
         * Создание стека вызова
         * @param array $trace Стек
         * @return array
         */
        protected static function backtrace($trace)
        {
            $backtrace = [];
            foreach (array_reverse($trace) as $data) {
                $args = [];
                if (isset($data['args']) && is_array($data['args']) && count($data['args']) > 0) {
                    $arguments = $data['args'];
                    foreach ($arguments as $argument) {
                        if ($argument instanceof \Exception) {
                            $bt = self::backtrace($argument->getTrace());
                            foreach ($bt as $b) {
                                $backtrace[] = $b;
                            }
                        }
                    }
                }
                $backtrace[] = [
                    'file'     => isset($data['file']) ? $data['file'] : '-',
                    'line'     => isset($data['line']) ? $data['line'] : '-',
                    'class'    => isset($data['class']) ? $data['class'] : '-',
                    'function' => isset($data['function']) ? $data['function'] : '-',
                    'type'     => isset($data['type']) ? ($data['type'] == '::' ? 'статический' : 'обычный') : '-',
                    'args'     => $args
                ];
            }
            return $backtrace;
        }

        /**
         * Вывод информации о перехваченной ошибке
         * @param string   $message   Текст ошибки
         * @param string   $file      Название файла
         * @param  string  $line      Строка
         * @param int      $http_code Код HTTP состояния
         */
        public static function show_error($message, $file, $line, $http_code = 500)
        {
            while (ob_get_level()) {
                ob_end_clean();
            }
            http_response_code($http_code);
            $error_stack = self::backtrace(debug_backtrace());
            $error_file = str_replace(PROJECT_PATH, '{PROJECT_PATH}' . _SEP, $file);
            $error_data = (DEBUG) ? $error_file . ':' . $line : 'скрыто';
            require_once CORE_PATH . 'ErrorHandler' . _SEP . 'templates' . _SEP . 'error.php';
            exit();
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
