<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Controller;

    /**
     * Class TemplateController
     * Контроллер для упрощения работы с шаблонами
     * Пытается найти шаблон, соответствующий названию экшена,
     * передает ему массив данных, полученных от экшена
     *
     * @package DMF\Core\Controller
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
