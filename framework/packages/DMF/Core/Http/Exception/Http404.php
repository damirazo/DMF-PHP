<?php

    namespace DMF\Core\Http\Exception;

    /**
     * Отправка ошибки 404
     */
    class Http404 extends HttpError
    {

        public function __construct($message) {
            parent::__construct($message, 404);
        }

    }
