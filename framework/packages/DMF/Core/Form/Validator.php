<?php

    namespace DMF\Core\Form;

    /**
     * Объект валидатора
     */
    class Validator
    {

        /** @var array Массив ошибок */
        protected $_errors = [];

        /**
         * Добавление ошибки валидации
         * @param string $error Текст ошибки
         */
        public function add_error($error)
        {
            $this->_errors[] = $error;
        }

        /**
         * Проверка валидности
         * @return bool
         */
        public function is_valid()
        {
            return !!(count($this->_errors) == 0);
        }

        /**
         * Возвращает первую полученную ошибку
         * @return bool
         */
        public function error()
        {
            return (count($this->_errors) > 0) ? $this->_errors[0] : false;
        }

        /**
         * Возвращает все ошибки, полученные при валидации поля
         * @return array
         */
        public function errors()
        {
            return $this->_errors;
        }

    }
