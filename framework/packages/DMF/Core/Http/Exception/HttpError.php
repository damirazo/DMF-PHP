<?php

    namespace DMF\Core\Http\Exception;

    /**
     * Базовое исключение для обработки ошибок HTTP
     */
    class HttpError extends \Exception
    {

        /** @var int Код HTTP состояния */
        protected $http_response_code;

        /** Конструктор */
        public function __construct($message, $http_code=500)
        {
            parent::__construct($message);
            $this->http_response_code = $http_code;
        }

        /** Возврат HTTP кода */
        public function get_http_code()
        {
            return $this->http_response_code;
        }

    }
