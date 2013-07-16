<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Controller;

    /**
     * Class JsonController
     * Данный контроллер используется для возврата данных с экшенов и преобразования их в JSON формат
     *
     * @package DMF\Core\Controller
     */
    class JsonController extends Controller
    {

        /**
         * Переопределение прокси-метода для перехвата ответа экшена
         * @param string $action Имя вызываемого экшена
         * @param mixed  $args   Аргументы, переданные экшену
         *
         * @return \DMF\Core\Http\Response|mixed
         */
        public function proxy($action, $args)
        {
            $response = parent::proxy($action, $args);
            return $this->json($response);
        }

    }
