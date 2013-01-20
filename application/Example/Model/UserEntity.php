<?php

    namespace App\Example\Model;

    use DMF\Auth\Model\DMFUserEntity;

    /**
     * Сущность модели пользователя
     */
    class UserEntity extends DMFUserEntity
    {

        public function hello()
        {
            return 'Приветствую, ' . $this->username . '! Дата регистрации: ' . $this->register_at;
        }

        public function update()
        {
            $this->register_at = date('Y-m-d H:i:s', time());
            $this->save();
        }

    }
