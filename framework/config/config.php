<?php

    /**
     * Файл системных настроек
     */

    use DMF\Core\Storage\Config;
    use DMF\Core\OS\OS;

    /** Активация дебагового режима */
    Config::set('debug', DEBUG);

    /** Список ip адресов, для которых будет доступна дебаговая информация */
    Config::set('allowed_ips', [
        '127.0.0.1'
    ]);

    /** Базовый хост сайта */
    Config::set('base_url', 'http://localhost/');

    /** Путь до статичных файлов */
    Config::set('static_url', 'http://localhost/static/');

    /** Настройки подключения к базе данных */
    Config::set(
        'database', [
            // Включение поддержки БД
            'enable'   => false,
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

    /** Кодовое слово для активации скаффолдинга в демонстрационных приложениях */
    Config::set('scaffolding_password', '123456qwerty');

    /** Импортирование локального конфига для переопределения системных настроек */
    OS::import(CONFIG_PATH . 'config.local.php', false);