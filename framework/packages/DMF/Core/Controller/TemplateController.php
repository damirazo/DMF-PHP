<?php

    namespace DMF\Core\Controller;

    /**
     * Контроллер, упрощающий работу с шаблонами
     */
    class TemplateController extends Controller
    {

        /**
         * Переопределение прокси метода
         * @param string $action Имя вызываемого действия
         * @param mixed  $args   Массив аргументов
         *
         * @return \DMF\Core\Template\Template|mixed
         */
        public function proxy($action, $args)
        {
            $response = parent::proxy($action, $args);
            return $this->render($action, $response);
        }

    }
