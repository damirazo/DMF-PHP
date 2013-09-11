<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Application\Exception;

    /**
     * Class BaseException
     * Базовый класс для всех исключений фреймворка
     *
     * @package DMF\Core\Application\Exception
     */
    class BaseException extends \Exception
    {

        /** Инициализация исключения */
        public function __construct($message = "", $code = 0, \Exception $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }

    }