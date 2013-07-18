<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\OS;

    use DMF\Core\OS\Exception\FileNotFound;

    /**
     * Class OS
     * Класс для взаимодействия с ОС
     *
     * @package DMF\Core\OS
     */
    class OS
    {

        /**
         * Проверка существования директории
         *
         * @param string $path Путь до директории
         * @return bool
         */
        public static function dir_exists($path)
        {
            return !!(file_exists($path) && is_dir($path));
        }

        /**
         * Импортирование PHP файла
         *
         * @param string  $path      Путь до файла
         * @param boolean $exception Требуется ли выбрасывать исключение, если файл отсутствует
         * @throws Exception\FileNotFound
         */
        public static function import($path, $exception = true)
        {
            if (self::file_exists($path)) {
                require_once $path;
            } elseif ($exception) {
                throw new FileNotFound('Файл ' . $path . ' не обнаружен!');
            }
        }

        /**
         * Проверка существования файла и доступности его для чтения
         *
         * @param string $path Путь до файла
         * @return bool
         */
        public static function file_exists($path)
        {
            return !!(is_readable($path));
        }

        /**
         * Возвращает рассчитанный путь
         *
         * @param string $path Путь до директории
         * @param string $file Имя файла
         * @return string
         */
        public static function join($path, $file)
        {
            return $path . _SEP . $file;
        }

        /**
         * Ищет все файлы, подходящие под указанный шаблон
         * Пример:
         * OS::search('/var/www/*.log')
         * Вернет все файлы, имеющие расширение log, находящиеся в указанной директории
         *
         * @param $pattern Шаблон для поиска
         * @return array
         */
        public static function search($pattern)
        {
            return glob($pattern);
        }

    }
