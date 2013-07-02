<?php

    echo c('Запуск синхронизации баз данных и моделей...');

    // Список зарегистрированных моделей
    $registered_models = ['App\Example\Model\Post'];

    // Если передано более двух аргументов, то значит были получены имена классов модели
    if (count($args) > 2) {
        $registered_models = [$args[2]];
    }

    echo c('Обнаружено моделей: ' . count($registered_models));

    foreach ($registered_models as $model) {
        echo c('Синхронизация модели: ' . $model);
        /** @var /DMF/Core/Model/Model $obj */
        $obj = new $model();
        $obj->create_table();
    }