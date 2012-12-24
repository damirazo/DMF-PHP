<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * JsonController.php
     * 19.11.12, 18:47
     */

    namespace DMF\Core\Controller;

    class JsonController extends Controller
    {

        public function proxy($action, $args)
        {
            $response = parent::proxy($action, $args);

            return $this->json($response);
        }

    }
