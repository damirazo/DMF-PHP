<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    use DMF\Core\Application\Application;
    use DMF\Core\Component\ComponentTypes;
    use DMF\Core\OS\OS;

    /**
     * Пример вызова данной команды:
     * Обновление структуры всех моделей
     * > php manager.php syncdb
     * Обновление структуры модели Post модуля Base
     * > php manager.php syncdb Base.Post
     * Обновление структуры всех моделей модуля Other
     * > php manager.php syncdb Other.*
     */

    format('Запуск синхронизации моделей...' . PHP_EOL);

    // Список модулей, подлежащих синхронизации
    $modules = [];

    // Инстанс приложения
    $app = Application::get_instance();

    // Обновление всех моделей всех модулей
    if (count($action_args) == 0) {
        $all_modules = $app->modules;
        foreach ($all_modules as $module) {
            $modules[] = $module->name . '.*';
        }
    } // Обновление структуры указанных модулей
    else {
        foreach ($action_args as $arg) {
            $modules[] = $arg;
        }
    }

    // Обработка модулей и формирование списка моделей для синхронизации
    $models = [];
    /** @var $m \DMF\Core\Module\Module */
    foreach ($modules as $module_signature) {
        list($module_name, $model_name) = explode('.', $module_signature);
        // Если указана синхронизация всех моделей, то достаем информацию о них из указанных модулей
        if ($model_name == '*') {
            $module = $app->get_module_by_name($module_name);
            $model_files_list = OS::search($module->path . 'Model' . _SEP . '*.php');
            foreach ($model_files_list as $path) {
                $path_segments = explode(_SEP, $path);
                $file_name = str_replace('.php', '', end($path_segments));
                $models[] = $app->get_component($module_name . '.' . $file_name, ComponentTypes::Model);
            }
        } // Если имя модели жестко прописано, то используем нативный способ доступа к ним
        else {
            $models[] = $app->get_component($module_signature, ComponentTypes::Model);
        }

    }

    format('Обнаружено моделей: %d', count($models));
    format('##################################');

    // Синхронизация моделей
    /** @var $model \DMF\Core\Model\Model */
    foreach ($models as $model) {
        format('Обработка модели: %s', $model->get_class_name());

        # Создание таблицы в БД
        $model->update_table();
        format('Создана таблица: %s', $model->table_name());

        # Загрузка фикстур
        format('Загрузка фикстур временно недоступна...');

        format('Синхронизация таблицы завершена...');
        format('##################################');
    }