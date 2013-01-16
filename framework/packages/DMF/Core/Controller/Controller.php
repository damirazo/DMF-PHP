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
            // метод получения запроса
            $method = strtolower(Request::get_instance()->get_method());
            // вначале проверяем наличия требуемого метода с префиксом в виде метода запроса
            if (method_exists($this, $action.'__'.$method)) {
                return call_user_func_array([$this, $action], $args);
            }
            // затем ищем метод по точному имени
            elseif (method_exists($this, $action)) {
                return call_user_func_array([$this, $action], $args);
            }
            // если метод не обнаружен, то генерируем исключение
            throw new ActionNotFoundException('Действие ' . $action . ' не обнаружено');
        }

    }
