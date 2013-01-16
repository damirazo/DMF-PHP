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

            /* Примеры */
            '/'                               => 'Example.Base.index',
            '/examples/'                      => 'Example.Base.examples',
            '/examples/hello/'                => 'Example.Base.example_hello',
            '/examples/var/'                  => 'Example.Base.example_var',
            '/examples/params/(@int)/(@int)/' => 'Example.Base.example_params',
            '/examples/form/'                 => 'Example.Base.example_form',

            /* Документация */
            '/doc/'                           => 'Doc.Base.index',
            '/doc/([\w\d\-]+).html/'          => 'Doc.Base.page',
        ]
    );