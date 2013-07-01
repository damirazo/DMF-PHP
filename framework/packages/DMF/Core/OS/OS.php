<?php

    namespace DMF\Core\OS;

    use DMF\Core\OS\Exception\FileNotFound;

    /**
     * Класс для взаимодействия с операционной системой
     * TODO: Завершить реализацию класса
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
         * @param string $path Путь до файла
         * @return bool
         */
        public static function file_exists($path)
        {
            return !!(is_readable($path));
        }

        public static function file_data($path, $as_array = false, $exception = true)
        {
            if (self::file_exists($path)) {
                $file = fopen($path, 'r');
                $data = fread($file, filesize($path));
                fclose($file);
                return $data;
            } elseif ($exception) {
                throw new FileNotFound('Файл ' . $path . ' не обнаружен!');
            }
            return false;
        }

    }
