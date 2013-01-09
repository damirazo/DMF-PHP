<?php

    namespace App\Example\Controller;

    use DMF\Core\Controller\Controller;
    use DMF\Core\Model\Entity;

    /**
     * Базовый тестовый контроллер
     */
    class Base extends Controller
    {

        /** Главная страница */
        public function index()
        {
            return $this->render('index');
        }

        /** Список примеров */
        public function examples()
        {
            return $this->render('examples');
        }

        /** Пример с простым приветствием */
        public function example_hello()
        {
            return $this->render('example_hello');
        }

        /** Пример с возвратом переменной в шаблон */
        public function example_var()
        {
            $datetime = date('Y-m-d H:i:s', time());
            return $this->render('example_var', ['datetime' => $datetime]);
        }

        /** Пример с динамическими параметрами в URI */
        public function example_params($param1, $param2)
        {
            return $this->render(
                'example_params',
                ['param1' => $param1, 'param2' => $param2, 'sum' => $param1 + $param2]
            );
        }

        public function register()
        {
//            $form = $this->form('Register');
//            $form->bound(['username' => 'damirazo']);

            $form = $this->form('PostEdit');
            $post = $this->model('Post')->get_by_pk(1);
            $form->bound($post);

            if ($form->is_received()) {
                if ($form->is_valid()) {
                    $this->model('Post')->update_by_pk($form->cleaned_data(), 1);
                }
            }

            return $this->render('register', ['form' => $form]);
        }

    }
