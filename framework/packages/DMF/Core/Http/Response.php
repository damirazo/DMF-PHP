<?php

    namespace DMF\Core\Http;

    /**
     * Класс для вывода контента пользователю
     */
    class Response
    {

        /**
         * Создание ответа сервера
         * @param string $content       Контент для отображения в браузере
         * @param int    $response_code HTTP код ответа
         * @param array  $headers       Список заголовков для передачи
         */
        public function __construct($content, $response_code = 200, $headers = [])
        {
            if (ob_get_level()) {
                ob_get_clean();
            }
            ob_start();
            foreach ($headers as $header) {
                header($header);
            }
            http_response_code($response_code);
            echo $content;
            ob_end_flush();
            exit();
        }

    }
