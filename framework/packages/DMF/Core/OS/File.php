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
     * Class File
     * Обертка над файловым дескриптором
     * Поддерживает цепочный вызов методов
     *
     * @package DMF\Core\OS
     */
    class File
    {

        /** @var string Путь к файлу */
        protected $file_path;
        /** @var null|resource */
        public $file_handler = null;

        /**
         * Инициализация объекта
         * @param string $file_path Путь к файлу
         * @throws FileNotFound
         */
        public function __construct($file_path)
        {
            $this->file_path = $file_path;
        }

        /**
         * Блокирование файла
         * @return $this
         */
        public function block()
        {
            flock($this->file_handler, LOCK_EX);
            return $this;
        }

        /**
         * Снятие блокировки с файла
         * @return $this
         */
        public function unblock()
        {
            flock($this->file_handler, LOCK_UN);
            return $this;
        }

        /**
         * Чтение всей информации из файла, либо указанное число символов
         * @param bool $limit Лимит чтения символов
         * @return string
         */
        public function read($limit = false)
        {
            $data = fread($this->file_handler, !$limit ? $this->size() : $limit);
            $this->close();
            return $data;
        }

        /**
         * Чтение информации из файла и возврат в виде массива строк
         * @return array
         */
        public function read_as_array()
        {
            $this->close();
            return file($this->file_path);
        }

        /**
         * Чтение информации в формате json и преобразование в массив
         * @return mixed
         */
        public function read_as_json()
        {
            $data = $this->read();
            return json_decode($data);
        }

        /**
         * Возвращает размер открытого файла
         * @return int
         */
        public function size()
        {
            return filesize($this->file_path);
        }

        /**
         * Запись данных в файл
         * @param string|array $data Данные для записи в виде строки, либо массива строк
         * @return $this
         */
        public function write($data)
        {
            fwrite($this->file_handler, $data);
            return $this;
        }

        /**
         * Открытие файла в требуемом режиме
         * @param string $mode Режим открытия файла
         * @return $this
         */
        public function open($mode = 'r')
        {
            $this->file_handler = fopen($this->file_path, $mode);
            return $this;
        }

        /**
         * Закрытие файла
         */
        public function close()
        {
            fclose($this->file_handler);
            $this->file_handler = null;
        }

    }
