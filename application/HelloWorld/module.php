<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    use DMF\Core\Application\Application;

    Application::register_module('HelloWorld', [
        'namespace'    => 'App\\HelloWorld',
        'author'       => 'damirazo',
        'version'      => '0.1',
    ]);