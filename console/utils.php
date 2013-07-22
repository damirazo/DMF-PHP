<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    /**
     * Отображение форматированной строки
     * @param string $str  Отображаемая строка
     * @param mixed  $args Список аргументов для строки
     */
    function format($str, $args = [])
    {
        // TODO: Под консоль windows требуется перекодирование сообщений в кодировку cp866
        if (!is_array($args)) {
            $args = [$args];
        }
        $data = array_merge([$str . PHP_EOL], $args);
        call_user_func_array('printf', $data);
    }