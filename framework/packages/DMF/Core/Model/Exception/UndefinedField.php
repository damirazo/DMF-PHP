<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */
    
    namespace DMF\Core\Model\Exception;
        
    use DMF\Core\Application\Exception\BaseException;

    /**
     * Class UndefinedField
     * Исключение генерируется при попытке обратится к неизвестному полю модели
     *
     * @package DMF\Core\Model\Exception
     */
    class UndefinedField extends BaseException
    {
    
    }