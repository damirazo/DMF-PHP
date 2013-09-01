<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    use DMF\Core\Application\Application;

    Application::register_routes([
        '/admin/'       => 'Admin.Base.index',
        '/admin/login/' => 'Admin.Base.login',
    ]);