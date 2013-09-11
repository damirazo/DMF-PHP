<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Http\Exception;

    /**
     * Class Http404
     * Исключение для отправки 404 ошибки
     *
     * @package DMF\Core\Http\Exception
     */
    class Http404 extends HttpError
    {

        public function __construct($message) {
            parent::__construct($message, 404);
        }

    }
