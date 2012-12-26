<?php

    /**
     * Все маршруты проекта
     */

    use DMF\Core\Router\Router;

    /**
     * Список маршрутов
     * Формат записи:
     * Ключ - регулярное выражение, которое может содержать псевдонимы для некоторых частей
     * Примеры псевдонимов: @int = [\d]+; @str = [\w]+; @alphanum = [\w\d]+; @all = '[\w\d\.\,\-\_\+]+;
     * Части регулярного выражения в круглых скобках будут переданы действию в качестве аргументов
     * Значение - строка с форматом записи "ИмяМодуля.ИмяКонтроллера.ИмяДействия"
     */
    Router::routes(
        [
            # Пример типичного маршрута (редактирование статьи)
            #'/post/(@int)/edit/' => 'Blog.Post.edit',

            '/'                               => 'Example.Base.index',
            '/examples/'                      => 'Example.Base.examples',
            '/examples/hello/'                => 'Example.Base.example_hello',
            '/examples/var/'                  => 'Example.Base.example_var',
            '/examples/params/(@int)/(@int)/' => 'Example.Base.example_params',
            '/examples/model/'                => 'Example.Base.example_model',
            '/examples/model/create/'         => 'Example.Base.example_model_create_data',
            '/examples/model/dump/'           => 'Example.Base.example_model_dump_data'
        ]
    );