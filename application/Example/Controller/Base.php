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
        public function post_edit($post_id)
        {
            // получение объекта формы
            $form = $this->form('PostEdit');
            // выборка статьи с айди 1
            $post = $this->model('Post')->get_by_pk($post_id);
            // отправка данных о статье в форму
            $form->bound($post);
            // проверяем была ли отправлена форма
            if ($form->is_received()) {
                // проверяем валидность формы
                if ($form->is_valid()) {
                    // обновляем статью
                    $this->model('Post')->update_by_pk($form->cleaned_data(), $post_id);
                    return $this->redirect('');
                }
            }
            // рендерим форму
            return $this->render('example_form', ['form' => $form]);
        }

        /** Отображение списка опубликованных статей */
        public function example_posts()
        {
            $posts = $this->model('Post')->get_all(['id', 'name']);
            return $this->render('example_posts', ['posts' => $posts]);
        }

        /** Создание таблицы в БД */
        public function db_update($given_password)
        {
            // Эталонное кодовое слово из конфигурации проекта
            $password = $this->config('scaffolding_password');
            // Проверка кодового слова
            $error = false;
            if (!$password) {
                $error = 'Не задано значение секретного слова для создания таблиц!';
            }
            if (!$given_password) {
                $error = 'Для доступа к системе создания таблиц требуется знать кодовое слово!';
            }
            if ($given_password != $password) {
                $error = 'Введено некорректное кодовое слово!';
            }
            if ($error) {
                return $this->string($error);
            }

            // Создание демонстрационных таблиц в БД
            $this->model('DMFAuth.DMFUser')->_create_table();
            $this->model('Post')->_create_table();

            return $this->string('Таблицы успешно создана!');
        }

        /** Регистрация нового пользователя */
        public function register()
        {
            // получаем объект формы
            $form = $this->form('Register');
            // проверяем факт отправки формы и валидность полученных данных
            if ($form->is_valid()) {
                // создаем новый объект пользователя и сохраняем его в БД
                $this->model('DMFAuth.DMFUser')->create($form->cleaned_data());
                // возвращаемся на главную страницу
                return $this->redirect('');
            }
            // выводим шаблон с формой
            return $this->render('register', ['form' => $form]);
        }

        /** Авторизация пользователя */
        public function login()
        {
            $form = $this->form('Login');
            if ($form->is_valid()) {
                $this->model('DMFAuth.DMFUser')->login($form->value('username'), $form->value('password'));
                return $this->redirect('user');
            }
            return $this->render('login', ['form' => $form]);
        }

        /** Информация о пользователе с указанным ID */
        public function user($user_id)
        {
            $user = $this->model('DMFAuth.DMFUser')->get_by_pk($user_id);
            if ($user) {
                return $this->string($user->username);
            }
            return $this->string('Указанный пользователь не обнаружен!');
        }

        /** Вывести информацию о пользователе */
        public function me()
        {
            return $this->render('user');
        }

    }
