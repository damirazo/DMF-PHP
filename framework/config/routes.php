<?php

    /**
     * Все маршруты проекта
     */
    use DMF\Core\Application\Application;

    /**
     * Список маршрутов
     * Формат записи:
     * Ключ - регулярное выражение, которое может содержать псевдонимы для некоторых частей
     * Примеры псевдонимов: @int = [\d]+; @str = [\w]+; @alphanum = [\w\d]+; @all = '[\w\d\.\,\-\_\+]+;
     * Части регулярного выражения в круглых скобках будут переданы действию в качестве аргументов
     * Значение - строка с форматом записи "ИмяМодуля.ИмяКонтроллера.ИмяДействия"
     */
    Application::routes(
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

            '/posts/'                         => 'Example.Base.posts',
            '/post/(@int)/'                   => 'Example.Base.post_view',
            '/post/(@int)/edit/'              => 'Example.Base.post_edit',
            '/post/(@int)/delete/'            => 'Example.Base.post_delete',
            '/post/new/'                      => 'Example.Base.post_new',

            '/db/update/([\w\d\$\-\_]+)/'     => 'Example.Base.db_update',

            '/user/'                          => 'Example.Base.me',
            '/user/(@int)/'                   => 'Example.Base.user',
            '/user/register/'                 => 'Example.Base.register',
            '/user/login/'                    => 'Example.Base.login',

            /* Документация */
            '/doc/'                           => 'Doc.Base.index',
            '/doc/([\w\d\-]+).html/'          => 'Doc.Base.page',

            // Просмотр лога ошибок фреймворка за текущий день
            '/admin/logs/today/'              => 'Admin.Base.current_access_log',
        ]
    );