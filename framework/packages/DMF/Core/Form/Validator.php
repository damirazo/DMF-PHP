<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form;

    /**
     * Class Validator
     * Валидатор поля формы
     * Используется для проверки корректности заполненности поля.
     *
     * @package DMF\Core\Form
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
