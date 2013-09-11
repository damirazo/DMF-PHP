<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Template;

    use DMF\Core\Template\Exception\TagElementExists;

    /**
     * Class Tag
     * Класс для хранения кастомных Twig тегов
     *
     * @package DMF\Core\Template
     */
    class Tag
    {

        /** @var array Список зарегистрированных тегов */
        public static $data = [];

        /**
         * Регистрация тега
         * @param \DMF\Core\Template\BaseTokenParser $tag_token_parser Экземпляр класса для парсинга тега
         * @throws Exception\TagElementExists
         */
        public static function register($tag_token_parser)
        {
            if (!in_array($tag_token_parser, self::$data)) {
                self::$data[] = $tag_token_parser;
            } else {
                throw new TagElementExists('Указанный тег уже был ранее зарегистрирован!');
            }
        }

    }