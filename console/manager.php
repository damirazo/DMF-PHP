<?php

    /**
     * Доступные аргументы:
     * syncdb [model] - Сгенерировать структуру БД для всех моделей [указанной модели]
     */

    // Загрузка компонента активации фреймворка
    require_once '../public/index.php';

    /** @var array $args Массив аргументов */
    $args = $_SERVER['argv'];

    /** @var string $version Версия консоли */
    $version = '0.1a';

    // Выводим отладочную информацию
    echo '--------------------------------------';
    echo 'DMF Interactive Console (DMFIC) v ' . $version;
    echo 'PHP ' . PHP_VERSION;
    echo '--------------------------------------';

    // Если обнаружен лишь один аргумент, то прерываем работу консоли
    if (count($args) <= 1) {
        exit('No arguments are specified to select the desired action!');
    }

    // Название запрашиваемого действия
    $action = $args[1];

    // Массив аргументов, без учета названия самого экшена
    $action_args = array_slice($args, 1);

    // Загрузка скрипта, реализующего требуемое действие
    switch ($action) {
        // Синхронизация таблиц в БД и моделей
        case 'syncdb':
            require_once 'syncdb.php';
            break;
        // Указанное действие не реализовано
        default:
            exit('Action ' . $action . ' is not specified!');
            break;
    }

    echo '--------------------------------------';
