<?php

    /** Проверяем, что версия PHP >= 5.4 */
    if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
        exit('Для корректной работы фреймворка необходима версия PHP >= 5.4');
    }

    /** Установка внутренней кодировки для мультибайтовых функций */
    mb_internal_encoding('utf8');

    /** Активация буфферизации вывода */
    ob_start();

    /** Старт сессий */
    session_start();

    /** Переключение рабочего окружения */
    define('ENVIRONMENT', 'prod');
    /** Определения рабочих окружений */
    switch(ENVIRONMENT) {
        /** Окружение для разработки */
        case 'dev':
            define('DEBUG', true);
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            break;
        /** Окружение для развертывания и тестирования */
        case 'test':
            define('DEBUG', true);
            error_reporting(E_ALL | ~E_NOTICE);
            ini_set('display_errors', 1);
            break;
        /** Рабочее окружение */
        case 'prod':
            define('DEBUG', false);
            error_reporting(E_ALL | ~E_NOTICE);
            ini_set('display_errors', 0);
            break;
    }

    /******************************
     *  СПИСОК СИСТЕМНЫХ КОНСТАНТ
     *****************************/

    /** Псевдоним для системного разделителя пути директорий */
    define('_SEP', DIRECTORY_SEPARATOR);
    /** Путь до корневой папки проекта */
    define('PROJECT_PATH', realpath(dirname(__FILE__) . _SEP . '..') . _SEP);
    /** Путь до папки с приложениями */
    define('APP_PATH', PROJECT_PATH . 'application' . _SEP);
    /** Путь до входной папки веб сервера */
    define('PUBLIC_PATH', PROJECT_PATH . 'public' . _SEP);
    /** Путь до папки с фреймворком */
    define('FRAMEWORK_PATH', PROJECT_PATH . 'framework' . _SEP);
    /** Путь до папки с библиотеками */
    define('PACKAGES_PATH', FRAMEWORK_PATH . 'packages' . _SEP);
    /** Путь до папки с системной конфигурацией */
    define('CONFIG_PATH', FRAMEWORK_PATH . 'config' . _SEP);
    /** Путь до папки записи служебной информации */
    define('DATA_PATH', FRAMEWORK_PATH . 'data' . _SEP);
    /** Путь до папки с основными библиотеками фреймворка */
    define('DMF_PATH', PACKAGES_PATH . 'DMF' . _SEP);
    /** Путь до папки с классами ядра фреймворка */
    define('CORE_PATH', DMF_PATH . 'Core' . _SEP);

    /** Импортирование загрузочного файла */
    require_once CONFIG_PATH . 'bootstrap.php';