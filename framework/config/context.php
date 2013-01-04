<?php

    /**
     * Глобальный шаблонный контекст
     * Переменные, указанные здесь, и их значения
     * будут переданы во все шаблоны автоматически
     */

    use DMF\Core\Template\Context;
    use DMF\Core\ErrorHandler\ErrorHandler;
    use DMF\Core\Http\Request;

    /** Загрузка и инициализация списка шаблонного контекста */

    /** Активирован ли режим дебага */
    Context::add('debug', DEBUG);
    /** Базовый URL сайта */
    Context::add('base_url', Request::get_instance()->base_url());
    /** Путь до папки статичных файлов */
    Context::add('static_url', Request::get_instance()->base_url() . '/static/');
    /** Рандомная строка в режиме разработки для добавления к именам статичных файлов */
    Context::add('random_string', DEBUG ? '?' . uniqid() : '');
    /** Стек вызовов */
    Context::add('backtrace', DEBUG ? ErrorHandler::show_backtrace() : '');