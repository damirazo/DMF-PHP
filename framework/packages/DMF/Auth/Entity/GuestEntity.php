<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Auth\Entity;

    use DMF\Core\Model\Entity;

    /**
     * Class GuestEntity
     * Описание сущности неавторизованного пользователя
     *
     * @package DMF\Auth\Model
     */
    class GuestEntity extends Entity
    {

        /**
         * Проверка авторизован ли пользователь
         * @return bool
         */
        public function is_authenticated()
        {
            return false;
        }

    }
