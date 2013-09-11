<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Template;

    use DMF\Core\Application\Exception\AbstractImplementation;

    /**
     * Class UrlTag
     *
     * @package DMF\Core\Template\Tag\UrlTag
     */
    class BaseTokenParser extends \Twig_TokenParser
    {

        public function getTag()
        {
            throw new AbstractImplementation();
        }

        public function parse(\Twig_Token $token)
        {
            throw new AbstractImplementation();
        }

    }