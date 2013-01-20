<?php

    namespace DMF\Auth\Model;

    use DMF\Core\Model\Entity;

    /**
     * Объект неавторизованного пользователя
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
