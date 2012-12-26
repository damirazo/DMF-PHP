<?php

    namespace DMF\Core\OS;

    /**
     * Класс-обертка над файловым дескриптором
     */
    class File
    {

        protected $file_path;

        public function __construct($file_path)
        {
            $this->file_path = $file_path;
        }

        protected function open($mode='r')
        {
            return fopen($this->file_path, $mode);
        }

        protected function close($file)
        {
            fclose($file);
        }

        public function as_string()
        {
            return file_get_contents($this->file_path);
        }

        public function as_array()
        {
            return file($this->file_path);
        }

        public function write($data)
        {
            $file = fopen($this->file_path, 'w');
            flock($file, LOCK_EX);
            fwrite($file, $data);
            flock($file, LOCK_UN);
            fclose($file);
        }

    }
