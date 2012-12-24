<?php

    /**
     * @package    DMF
     * @subpackage Template
     * @author     damirazo <damirazo.kazan@gmail.com>
     * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
     * @version    0.1.0
     * @since      Класс для обработки шаблонов
     */

    namespace DMF\Core\Template;

    use DMF\Core\Component\Component;
    use DMF\Core\Http\Response;

    class Template extends Component
    {

        /** @var string Имя шаблона */
        protected $template_name;

        /** @var array Массив данных для шаблона */
        protected $data;

        /** @var int Код HTTP ответа */
        protected $http_response_code;

        /**
         * Инициализация объекта шаблона
         * @param string      $template_name      Имя шаблона
         * @param array       $data               Массив данных для шаблона
         * @param int         $http_response_code Код HTTP ответа
         */
        public function __construct($template_name, $data = [], $http_response_code = 200)
        {
            $this->template_name = $template_name;
            $this->data = $data;
            $this->http_response_code = $http_response_code;

            return $this->render();
        }

        /**
         * Рендеринг шаблона
         * @return \DMF\Core\Http\Response
         */
        public function render()
        {
            $debug = $this->config('debug');
            // Объект загрузчика
            $loader = new \Twig_Loader_Filesystem(
                APPLICATION_ROOT . $this->get_module_name() . SEPARATOR . 'View'
            );
            // Объект окружения
            $twig = new \Twig_Environment($loader, [
                'cache' => DATA_ROOT . 'templates_cache',
                'debug' => $debug
            ]);

            // Глобальные переменные
            foreach (Context::$_data as $name => $value) {
                $twig->addGlobal($name, $value);
            }

            // Возвращаем отрендеренный шаблон
            $response = $twig->render($this->template_name . '.twig', $this->data);

            return new Response($response, $this->http_response_code);
        }

    }
