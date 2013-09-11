<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Storage;

    use DMF\Core\Security\Crypt;

    /**
     * Class Cookie
     * Класс для работы с cookies
     *
     * @package DMF\Core\Storage
     */
    class Cookie
    {

        /**
         * Задает значение cookie
         *
         * @param string $name      Название cookie
         * @param string $value     Значение cookie
         * @param int    $expire    Срок действия cookie в секундах
         * @param null   $path      Корневой путь для установки cookie
         * @param null   $domain    Домен для cookie
         * @param null   $secure    Использовать для установки cookie лишь защищенное (https) соединение
         * @param null   $http_only Передавать cookie лишь при запросе по http протоколу
         */
        public static function set(
            $name,
            $value,
            $expire = null,
            $path = null,
            $domain = null,
            $secure = null,
            $http_only = null
        ) {
            if (self::allow()) {
                setcookie($name, $value, $expire, $path, $domain, $secure, $http_only);
            }
        }

        /**
         * Задает значение cookie, шифрует и упаковывает его
         *
         * @param string $name      Название cookie
         * @param string $value     Значение cookie
         * @param int    $expire    Срок действия cookie в секундах
         * @param null   $path      Корневой путь для установки cookie
         * @param null   $domain    Домен для cookie
         * @param null   $secure    Использовать для установки cookie лишь защищенное (https) соединение
         * @param null   $http_only Передавать cookie лишь при запросе по http протоколу
         */
        public static function set_crypt(
            $name,
            $value,
            $expire = null,
            $path = null,
            $domain = null,
            $secure = null,
            $http_only = null
        ) {
            $data = Crypt::base64_encode(Crypt::encrypt($value));
            self::set($name, $data, $expire, $path, $domain, $secure, $http_only);
        }

        /**
         * Проверить существование cookie с именем $name
         *
         * @param string $name Имя cookie
         *
         * @return bool Если cookie с именем $name найдена, то вернуть true, в противном случае false
         */
        public static function has($name)
        {
            return !!(isset($_COOKIE[$name]));
        }

        /**
         * Вернуть значение cookie с именем $name
         * Вернуть значение $default, если cookie с именем $name не обнаружено
         *
         * @param string $name    Имя cookie
         * @param mixed  $default Значение, возвращаемое по умолчанию
         *
         * @return mixed|bool Значение cookie с именем $name или значение $default, если cookie не найдено
         */
        public static function get($name, $default = false)
        {
            if (self::has($name)) {
                return $_COOKIE[$name];
            }
            return $default;
        }

        /**
         * Вернуть расшифрованное значение cookie с именем $name
         * Вернуть значение $default, если cookie с именем $name не обнаружено
         *
         * @param string $name    Имя cookie
         * @param mixed  $default Значение, возвращаемое по умолчанию
         *
         * @return mixed|bool Значение cookie с именем $name или значение $default, если cookie не найдено
         */
        public static function get_crypt($name, $default = false)
        {
            $data = self::get($name, $default);
            if ($data !== $default) {
                return Crypt::decrypt(Crypt::base64_decode($data));
            }
            return $default;
        }

        /**
         * Проверить возможность задания cookie в браузере клиента
         *
         * @return bool Возвращает true, если cookie можно установить, в противном случае false
         */
        public static function allow()
        {
            if (setcookie('test', 'worked!')) {
                self::delete('test');
                return true;
            }
            return false;
        }

        /**
         * Удаление cookie с именем $name
         *
         * @param string $name Имя cookie
         */
        public static function delete($name)
        {
            if (self::has($name)) {
                setcookie($name, null, -1);
            }
        }

    }
