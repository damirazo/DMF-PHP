<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Application\Exception;

    /**
     * Class AbstractImplementation
     * Указанный метод требует переопределения в дочерних классах
     *
     * @package DMF\Core\Application\Exception
     */
    class AbstractImplementation extends BaseException
    {

        public function __construct($message = false)
        {
            if (!$message) {
                $message = 'Указанный метод требуется переопределить в дочернем классе!';
            }
            parent::__construct($message);
        }

    }