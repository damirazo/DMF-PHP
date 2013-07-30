<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Template\Tag\UrlTag;

    use DMF\Core\Template\BaseTokenParser;

    /**
     * Class UrlTag
     * Парсинг тега url
     *
     * @package DMF\Core\Template\Tag\UrlTag
     */
    class UrlTagTokenParser extends BaseTokenParser
    {

        /**
         * Выполнение парсинга тега
         * @param \Twig_Token $token
         * @return UrlTagNode|void
         */
        public function parse(\Twig_Token $token)
        {
            // Формат записи тега
            // {% url 'Example.Base.index' var1,var2 %}
            $parser = $this->parser;
            $stream = $parser->getStream();

            // Путь до экшена
            $path = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();

            // Формирование списка аргументов
            $arguments = [];
            while (true) {
                $token = $stream->expect(\Twig_Token::NAME_TYPE);
                $arguments[] = trim($token->getValue());
                if (!$stream->test(\Twig_Token::PUNCTUATION_TYPE, ',')) {
                    break;
                }
                $stream->next();
            }

            $stream->expect(\Twig_Token::BLOCK_END_TYPE);

            return new UrlTagNode(
                [],
                ['path' => $path, 'args' => $arguments],
                $token->getLine(),
                $this->getTag()
            );
        }

        public function getTag()
        {
            return 'url';
        }

    }