<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */    
        
    namespace DMF\Core\Event\Exception;

    use DMF\Core\Application\Exception\BaseException;

    /**
     * Class EventExists
     * Исключение, выбрасываемое при попытке повторной регистрации события с тем же именем
     *
     * @package DMF\Core\Event\Exception
     */
    class EventExists extends BaseException
    {
    
    }