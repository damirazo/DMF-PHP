<?php

    namespace DMF\Core\Form;

    /**
     * Класс валидатора поля
     */
    class Validator
    {

        /** @var bool Проверка валидатора на валидность */
        protected $is_valid = true;

        /** @var null|string Сообщение об ошибке */
        protected $error = null;

        /**
         * Добавление ошибки в валидатор
         * @param string $error Текст ошибки
         */
        public function add_error($error)
        {
            $this->is_valid = false;
            $this->error = $error;
        }

        /**
         * Получение сообщения об ошибке
         * @return null|string
         */
        public function get_error()
        {
            return $this->error;
        }

        /**
         * Возвращает данные о том является ли валидатор валидным
         * @return bool
         */
        public function is_valid()
        {
            return $this->is_valid;
        }

    }
