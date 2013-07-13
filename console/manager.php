<?php

    /**
     * Доступные аргументы:
     * syncdb [model] - Сгенерировать структуру БД для всех моделей [указанной модели]
     */

    // Загрузка компонента активации фреймворка
    require_once '../framework/config/bootstrap.php';

    /** @var array $args Массив аргументов */
    $args = $_SERVER['argv'];

    /** @var string $version Версия консоли */
    $version = '0.1a';

    // Выводим отладочную информацию
    print('--------------------------------------');
    print('DMF Interactive Console (DMFIC) v ' . $version);
    print('PHP ' . PHP_VERSION);
    print('--------------------------------------');

    // Если обнаружен лишь один аргумент, то прерываем работу консоли
    if (count($args) <= 1) {
        die(print('No arguments are specified to select the desired action!'));
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
            print('Action ' . $action . ' is not specified!');
            break;
    }

    print('--------------------------------------');
