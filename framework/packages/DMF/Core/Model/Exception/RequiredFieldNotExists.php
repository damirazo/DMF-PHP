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
     * Class RequiredFieldNotExists
     * Выбрасывается при отсутствии значения у обязательного поля сущности
     *
     * @package DMF\Core\Model\Exception
     */
    class RequiredFieldNotExists extends BaseException
    {
    
    }