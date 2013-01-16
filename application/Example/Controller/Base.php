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

        /** Пример работы с формами */
        public function example_form()
        {
            //            // получение объекта формы
            //            $form = $this->form('PostEdit');
            //            // выборка статьи с айди 1
            //            $post = $this->model('Post')->get_by_pk(1);
            //            // отправка данных о статье в форму
            //            $form->bound($post);
            //            // проверяем была ли отправлена форма
            //            if ($form->is_received()) {
            //                // проверяем валидность формы
            //                if ($form->is_valid()) {
            //                    // обновляем статью
            //                    $this->model('Post')->update_by_pk($form->cleaned_data(), 1);
            //                    return $this->redirect('');
            //                }
            //            }
            //            // рендерим форму
            //            return $this->render('example_form', ['form' => $form]);

            return $this->xml(
                [
                    'rss' => [
                        'version'     => '2.0',
                        '@attributes' => ['version' => 1, 'style' => 'color:red;'],
                        'channel'     => [
                            'data'        => 500,
                            'title'       => ['test' => true, 'fgdfgdf' => 'тестовый элемент'],
                            'link'        => 'http://damirazo.ru',
                            'description' => 'dfgk dfg as fg hdfgdf rt dfgdfgsad fgghgfh'
                        ]
                    ]
                ]
            );
        }

    }
