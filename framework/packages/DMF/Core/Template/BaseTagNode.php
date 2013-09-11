<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Template;

    /**
     * Class BaseTagNode
     * Базовый класс, описывающие ноды тегов для шаблонов Twig
     *
     * @package DMF\Core\Template\Tag
     */
    class BaseTagNode extends \Twig_Node
    {

        public function __construct($node_name, $value, $line, $tag_name=null)
        {
            parent::__construct($node_name, $value, $line, $tag_name=null);
        }

    }