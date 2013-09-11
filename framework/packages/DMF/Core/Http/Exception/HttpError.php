<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Http\Exception;

    use DMF\Core\Application\Exception\BaseException;

    /**
     * Class HttpError
     * Базовое исключение для возврата HTTP кодов
     *
     * @package DMF\Core\Http\Exception
     */
    class HttpError extends BaseException
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
