<?php

    /**
     * Активация перехватчика ошибок и автозагрузчика
     * должна производится до момента запуска фреймворка и инициализации приложения
     * Порядок следования импортов и запуска методов важен
     */

    /** Импортирование перехватчика ошибок */
    require_once CORE_PATH . 'ErrorHandler' . _SEP . 'ErrorHandler.php';
    /** Импортирование автозагрузчика */
    require_once CORE_PATH . 'Autoloader' . _SEP . 'Autoloader.php';

    use DMF\Core\Application\Application;
    use DMF\Core\Autoloader\Autoloader;
    use DMF\Core\ErrorHandler\ErrorHandler;

    /**
     * Регистрация корневых пространств имен
     * Формат записи:
     * Ключ - Корень пространства имен (часть до первого символа \)
     * Значение - Путь до директории верхнего уровня, с которого будет производится поиск классов
     */
    Autoloader::register_namespaces([
        // Дефолтное пространство имен для приложений
        'App'  => APP_PATH,
        // Дефолтное пространство имен фреймворка
        'DMF'  => DMF_PATH,
        // Пространство имен ORM фреймворка
        'ORM'  => PACKAGES_PATH . 'ORM' . _SEP . 'src',
        // Пространство имен шаблонизатора Twig
        'Twig' => PACKAGES_PATH . 'Twig' . _SEP . 'lib' . _SEP . 'Twig',
    ]);

    // Инициализация обработчика ошибок до инициализации приложения
    ErrorHandler::run();

    // Получение экземпляра объекта приложения
    Application::get_instance()
        // Поиск и загрузка модулей
        ->load_modules()
        // Поиск и загрузка маршрутов
        ->load_routes()
        // Инициализация приложения
        ->run();