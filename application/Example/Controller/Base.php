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
         */
        public function index()
        {
            return $this->render('index');
        }

        /**
         * Список примеров
         */
        public function examples()
        {
            return $this->render('examples');
        }

    }
