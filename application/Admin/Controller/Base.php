<?php

    namespace App\Admin\Controller;

    use DMF\Core\Controller\Controller;
    use DMF\Core\Storage\Session;

    /**
     * Class Base
     * Базовый контроллер
     *
     * @package App\Admin\Controller
     */
    class Base extends Controller
    {

        /**
         * Переопределение прокси метода для создания авторизации
         * @param string $action
         * @param mixed  $args
         * @return mixed|void
         */
        public function proxy($action, $args)
        {
            if ($this->model('DMFAuth.DMFUser')->check_auth()) {
                return parent::proxy($action, $args);
            } else {
                return $this->call_controller('user_login');
            }
        }

        /**
         * Выполнение авторизации пользователя
         * @return \DMF\Core\Http\Response
         */
        public function user_login()
        {
            return $this->string('Здесь будет страница с запросом авторизации пользователя...');
        }

        /**
         * Просмотр списка логов
         * @return \DMF\Core\Http\Response
         */
        public function current_access_log()
        {
            return $this->string('Здесь будет страница со списком логов...');
        }

    }