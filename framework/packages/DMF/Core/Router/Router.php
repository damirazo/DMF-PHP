<?php

    namespace DMF\Core\Router;

    use DMF\Core\Router\Exception\RouteExists;

    /**
     * Класс маршрутизатора
     */
    class Router
    {

        /** @var array Список маршрутов */
        private static $_routes = [];

        /**
         * Регистрация списка маршрутов
         * @param array $routes Список маршрутов
         * @throws Exception\RouteExists
         */
        public static function routes($routes = [])
        {
            foreach ($routes as $pattern => $callable) {
                if (!isset(self::$_routes[$pattern])) {
                    self::$_routes[$pattern] = new Pattern($callable);
                }
                else {
                    throw new RouteExists('Маршрут ' . $pattern . ' уже был задан ранее для действия ' . $callable);
                }
            }
        }

        /**
         * read only доступ к списку маршрутов
         * @return array
         */
        public static function data()
        {
            return self::$_routes;
        }

    }
