<?php

    namespace App\Doc\Controller;

    use DMF\Core\Controller\Controller;
    use DMF\Core\OS\OS;
    use DMF\Core\Http\Exception\Http404;

    /**
     * Базовый контроллер для документации
     */
    class Base extends Controller
    {

        /** Главная страница */
        public function index()
        {
            return $this->render('index');
        }

        /** Все остальные страницы */
        public function page($name)
        {
            // Путь к директории с шаблонами
            $pages_dir = self::$app->module->path . 'View' . _SEP;
            // Проверяем наличие шаблона, чтобы переопределить поведение по умолчанию для шаблонизатора
            if (OS::file_exists($pages_dir . $name . '.twig')/* && $name != 'base'*/) {
                return $this->render($name);
            }
            // Генерируем 404 ошибку, если страницы с таким именем не существует
            throw new Http404('Указанная страница отсутствует на сайте!');
        }

    }
