<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    \DMF\Core\Application\Application::register_routes([
        '/index/' => 'Example.Base.index',
    ]);