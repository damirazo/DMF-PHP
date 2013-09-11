<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace App\HelloWorld\Controller;
        
    use DMF\Core\Controller\Controller;

    /**
     * Class Base
     * Базовый контроллер
     *
     * @package App\HelloWorld\Controller
     */
    class Base extends Controller
    {

        /**
         * Тестовое действие
         * @return \DMF\Core\Http\Response
         */
        public function index()
        {
            $string = $this->config('name') ? $this->config('name') : 'World';
            $hello = sprintf('Hello, %s!', $string);
            return $this->string($hello);
        }

    }