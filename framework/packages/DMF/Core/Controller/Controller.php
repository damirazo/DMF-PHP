<?php

    namespace DMF\Core\Controller;

    use DMF\Core\Component\Component;
    use DMF\Core\Http\Request;
    use DMF\Core\Storage\Config;
    use DMF\Core\Controller\Exception\ActionNotFoundException;

    /**
     * Базовый контроллер
     */
    class Controller extends Component
    {

        /**
         * Прокси метод для вызова действий
         * @param string $action Имя действия
         * @param mixed  $args   Список аргументов, переданных действию
         * @return mixed
         * @throws Exception\ActionNotFoundException
         */
        public function proxy($action, $args)
        {
            if (method_exists($this, $action)) {
                return call_user_func_array([$this, $action], $args);
            }
            throw new ActionNotFoundException('Действие ' . $action . ' не обнаружено');
        }

    }
