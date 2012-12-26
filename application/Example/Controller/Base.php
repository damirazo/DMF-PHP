<?php

    namespace App\Example\Controller;

    use DMF\Core\Controller\Controller;

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

        public function example_model()
        {
            $this->model('Post')->_update_table();
            return $this->string('Модель успешно создана, фикстуры загружены!');
        }

        public function example_model_create_data()
        {
            for ($i = 0; $i < 5; $i++) {
                $data = [
                    'name'   => 'Тестовая статья №' . $i,
                    'text'   => 'Nullam varius lectus et felis tincidunt molestie. Cras luctus nunc lacus, vel accumsan
                                quam. Quisque non sapien ut tellus tincidunt feugiat. Phasellus quis varius est. Quisque
                                mollis varius imperdiet. Quisque ac scelerisque eros. Vestibulum ac ipsum nibh, eu porta
                                neque.',
                    'status' => true
                ];
                $this->model('Post')->create($data);
            }
            return $this->string('Все объекты успешно сохранены в БД');
        }

        public function example_model_dump_data()
        {
            $this->model('Post')->_dump_fixtures();
            return $this->string('Данные из таблицы в БД успешно выгружены в фикстуры!');
        }

    }
