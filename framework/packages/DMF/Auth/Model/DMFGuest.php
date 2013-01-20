<?php

    namespace DMF\Auth\Model;

    /**
     * Объект неавторизованного пользователя
     */
    class DMFGuest
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
