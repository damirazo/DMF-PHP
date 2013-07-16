<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\DMFAuth\Model;

    use DMF\Core\Model\Entity;

    /**
     * Class DMFGuestEntity
     * Описание сущности неавторизованного пользователя
     *
     * @package DMF\DMFAuth\Model
     */
    class DMFGuestEntity extends Entity
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
