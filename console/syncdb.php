<?php

    echo c('Starting synchronization of databases and models...');

    // Список зарегистрированных моделей
    $registered_models = ['App\Example\Model\Post'];

    // Если передано более двух аргументов, то значит были получены имена классов модели
    if (count($args) == 2) {
        $registered_models = [$args[2]];
    }

    echo c('Models found: ' . count($registered_models));

