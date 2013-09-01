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
     * Class DMFUserEntity
     * Сущность авторизованного пользователя
     *
     * @package DMF\Auth\Model
     */
    class UserEntity extends Entity
    {

        /**
         * Проверка - является ли пользователь администратором
         * @return bool
         */
        public function is_admin()
        {
            return !!($this->data['access_level'] >= 100);
        }

        /**
         * Проверка авторизован ли пользователь на сайте
         * @return bool
         */
        public function is_authenticated()
        {
            return true;
        }

    }
