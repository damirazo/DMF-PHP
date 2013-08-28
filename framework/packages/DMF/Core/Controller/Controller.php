<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Controller;

    use DMF\Core\Application\Exception\IllegalArgument;
    use DMF\Core\Component\Component;
    use DMF\Core\Component\ComponentTypes;
    use DMF\Core\Controller\Exception\ActionNotFound;
    use DMF\Core\Http\Request;
    use DMF\Core\Storage\Config;

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
         * @throws Exception\ActionNotFound
         */
        public function proxy($action, $args)
        {
            // метод получения запроса
            $method = strtolower(Request::get_instance()->get_method());
            // вначале проверяем наличия требуемого метода с префиксом в виде метода запроса
            if (method_exists($this, $action . '__' . $method)) {
                return call_user_func_array([$this, $action . '__' . $method], $args);
            } // затем ищем метод по точному имени
            elseif (method_exists($this, $action)) {
                return $this->action($action, $args);
            }
            // если метод не обнаружен, то генерируем исключение
            throw new ActionNotFound('Действие ' . $action . ' не обнаружено');
        }

        /**
         * Вызов действия из указанного модуля и контроллера и возврат результата их вызова
         * @param string $callable Имя действия
         * @param array  $args Массив аргументов
         * @return mixed
         * @throws
         */
        protected function action($callable, $args = [])
        {
            $app = self::$app;
            $segments = explode('.', $callable);
            /**
             * Разбор списка переданных параметров
             * Если передано 3 параметра, то достаем имя модуля, имя контроллера и имя экшена
             * Если передано 2 параметра, то берем текущее имя модуля
             * Если передан один параметр, то достаем текущие имя модуля и имя контроллера
             */
            if (count($segments) == 3) {
                $module = $segments[0];
                $query = $module . '.' . $segments[1];
                $action = $segments[2];
            } elseif (count($segments) == 2) {
                $module = self::$app->module_name();
                $query = $module . '.' . $segments[0];
                $action = $segments[1];
            } elseif (count($segments) == 1) {
                $module = self::$app->module_name();
                $controller = $this->class_name();
                $query = $module . '.' . $controller;
                $action = $segments[0];
            } else {
                throw new IllegalArgument('Некорректный формат записи имени действия!');
            }

            $controller = self::$app->get_component($query, ComponentTypes::Controller);
            if (method_exists($controller, $action)) {
                // Для правильного выполнения экшена из другого модуля требуется
                // на время выполнения экшена переопределить активный модуль
                $old_module = $app->module;
                $app->module = $app->get_module_by_name($module);
                $result = call_user_func_array([$controller, $action], $args);
                $app->module = $old_module;
                return $result;
            } else {
                throw new ActionNotFound('Указанный метод не обнаружен!');
            }
        }

    }
