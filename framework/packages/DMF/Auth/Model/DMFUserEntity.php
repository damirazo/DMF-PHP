<?php

    namespace DMF\Auth\Model;

    use DMF\Core\Model\Entity;

    /**
     * Базовая сущность объекта пользователя
     */
    class DMFUserEntity extends Entity
    {

        /**
         * Проверка - является ли пользователь администратором
         * @return bool
         */
        public function is_admin()
        {
            return !!($this->data['access_level'] >= 100);
        }

    }
