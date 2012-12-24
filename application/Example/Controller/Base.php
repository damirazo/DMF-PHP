<?php

    namespace App\Example\Controller;

    use DMF\Core\Controller\Controller;

    /**
     * Базовый тестовый контроллер
     */
    class Base extends Controller
    {

        /**
         * Главная страница
         * @return \DMF\Core\Http\Response
         */
        public function index()
        {
            return $this->string('Привет, Мир!');
        }

    }
