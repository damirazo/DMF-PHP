<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Template\Exception;

    use DMF\Core\Application\Exception\BaseException;

    /**
     * Class TagElementExists
     * Данный кастомный тег уже был ранее зарегистрирован
     *
     * @package DMF\Core\Template
     */
    class TagElementExists extends BaseException
    {

    }