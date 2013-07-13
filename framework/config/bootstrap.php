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

    /** Импорт маршрутов */
    require_once 'routes.php';

    /**
     * Регистрация корневых пространств имен
     * Формат записи:
     * Ключ - Корень пространства имен (часть до первого символа \)
     * Значение - Путь до директории верхнего уровня, с которого будет производится поиск классов
     */
    // TODO: Придумать что-то с пространством имен, возможно автогенерацию...
    Autoloader::register_namespaces([
        // Дефолтное пространство для приложений
        'App'  => APP_PATH,
        // Дефолтное пространство фреймворка
        'DMF'  => DMF_PATH,
        // Пространство имен шаблонизатора Twig (PSR-0 формат)
        'Twig' => PACKAGES_PATH . 'Twig' . _SEP . 'lib' . _SEP . 'Twig'
    ]);

    // Инициализация обработчика ошибок до инициализации приложения
    ErrorHandler::run();

    /**
     * Регистрация списка модулей и их пространство имен
     * Формат записи:
     * Ключ - Имя папки с модулем (и имя модуля)
     * Значение - Пространство имен корня модуля
     */
    Application::get_instance()->register_modules([
        'Example' => 'App\\Example',
        'Doc'     => 'App\\Doc',
        'Admin'   => 'App\\Admin',
        'DMFAuth' => 'DMF\\DMFAuth'
    ]);

    /** Активация приложения */
    Application::get_instance()->run();