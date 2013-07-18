<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    use DMF\Core\Application\Application;
    use DMF\Core\OS\OS;

    /**
     * Пример вызова данной команды:
     *
     * Обновление структуры всех моделей
     * > php manager.php syncdb
     *
     * Обновление структуры модели Post модуля Base
     * > php manager.php syncdb Base.Post
     *
     * Обновление структуры всех моделей модуля Other
     * > php manager.php syncdb Other.*
     */

    format('Запуск синхронизации моделей...' . PHP_EOL);

    // Список моделей, подлежащих синхронизации
    $modules = [];

    // Инстанс приложения
    $app = Application::get_instance();

    // Обновление всех моделей всех модулей
    if (count($action_args) == 0) {
        $all_modules = $app->modules;
        foreach ($all_modules as $module) {
            $modules[] = $module->name . '.*';
        }
    }
    // Обновление структуры указанных модулей
    else {
        foreach ($action_args as $arg) {
            $modules[] = $arg;
        }
    }

    // Обработка модулей и формирование списка моделей для синхронизации

    // Синхронизация моделей


