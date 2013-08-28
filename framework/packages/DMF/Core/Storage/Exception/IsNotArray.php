<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */    
        
    namespace DMF\Core\Storage\Exception;

    use DMF\Core\Application\Exception\BaseException;

    /**
     * Class IsNotArray
     * Генерация исключения при попытке обратиться к элементк конфигурации, как к массиву
     *
     * @package DMF\Core\Storage\Exception
     */
    class IsNotArray extends BaseException
    {
    
    }