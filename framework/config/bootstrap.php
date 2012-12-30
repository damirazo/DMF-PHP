<?php

    /**
     * Данный файл подключается в точку входа (index.php)
     * и используется для активации компонентов фреймворка
     */

    /** Импорт перехватчика ошибок и автозагрузчика */
    require_once 'autoload.php';
    /** Импорт маршрутов */
    require_once 'routes.php';

    /** Импорт базового класса приложения */
    use DMF\Core\Application\Application;

    /**
     * Регистрация списка модулей и их пространство имен
     * Формат записи:
     * Ключ - Имя папки с модулем (и имя модуля)
     * Значение - Пространство имен корня модуля
     */
    Application::get_instance()->register_modules(
        [
            'Example' => 'App\\Example',
            'Doc'     => 'App\\Doc',
            'Auth'    => 'DMF\\Auth'
        ]
    );

    /** Активация приложения */
    Application::get_instance()->run();