<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    use DMF\Core\Template\Tag;
    use DMF\Core\Template\Tag\UrlTag\UrlTagTokenParser;

    /**
     * Тег "url"
     * Используется для подстановки url,
     * связанного с указанным экшеном
     */
    Tag::register(new UrlTagTokenParser());