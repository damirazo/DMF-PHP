<?php

    /**
     * Файл системных настроек
     */

    use DMF\Core\Storage\Config;
    use DMF\Core\OS\OS;

    /** Активация дебагового режима */
    Config::set('debug', DEBUG);

    /** Настройки подключения к базе данных */
    Config::set(
        'database', [
            // Включение поддержки БД
            'enable'   => true,
            // Драйвер для подключения к БД (в настоящий момент не поддерживается)
            'driver'   => 'mysql',
            // Хост БД
            'host'     => '127.0.0.1',
            // Порт БД
            'port'     => 3306,
            // Логин пользователя БД
            'user'     => 'root',
            // Пароль пользователя БД
            'password' => '123456',
            // Имя БД по умолчанию
            'name'     => 'framework',
            // Префикс к именам таблиц в БД
            'prefix'   => 'dm_'
        ]
    );

    /** Секретный ключ для алгоритмов шифрования */
    Config::set('secret_key', 'Kdl39&34m<dfk)fkd3sdfLSD)ds73mf,,sdf');

    /** Импортирование локального конфига для переопределения системных настроек */
    if (DEBUG) {
        OS::import(CONFIG_PATH . 'config.local.php', false);
    }