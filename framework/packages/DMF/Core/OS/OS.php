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
         * @param null|string Кастомное сообщение об отсутствии файла
         * @throws Exception\FileNotFound
         */
        public static function import($path, $exception = true, $exc_message = null)
        {
            if (self::file_exists($path)) {
                require_once $path;
            } elseif ($exception) {
                $msg = (is_null($exc_message)) ? 'Файл %s не обнаружен!' : $exc_message;
                throw new FileNotFound(sprintf($msg, $path));
            }
        }

        /**
         * Проверка существования файла и доступности его для чтения
         * @param string $path Путь до файла
         * @return bool
         */
        public static function file_exists($path)
        {
            return !!(is_readable($path));
        }

        /**
         * Возвращает рассчитанный путь
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

        /**
         * Возвращает список директорий, вложенных в указанную директорию
         * @param string $root Директория, внутри которой будет производится поиск
         * @return array
         */
        public static function dirs($root)
        {
            $data_list = scandir($root);
            $ignore_List = ['.', '..'];
            $dirs = [];
            foreach ($data_list as $element) {
                if (is_dir($root . $element) && !in_array($element, $ignore_List)) {
                    $dirs[] = $root . $element . _SEP;
                }
            }
            return $dirs;
        }

    }
