<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * Crypt.php
     * 07.11.12, 23:41
     */

    namespace DMF\Core\Security;

    use DMF\Core\Storage\Config;
    use DMF\Core\Security\Exception\UnsupportedAlgorithmException;

    /**
     * Класс для работы с шифрованными и хэшированными данными
     */
    class Crypt
    {

        /**
         * Шифрует данные, переданные в аргументе $data
         * Использует значение параметра secret_key в качестве ключа
         *
         * @param string $data Значение для шифрования
         *
         * @return string Зашифрованные данные
         */
        public static function encrypt($data)
        {
            return mcrypt_encrypt(MCRYPT_BLOWFISH, Config::get('secret_key'), $data, MCRYPT_MODE_ECB);
        }

        /**
         * Дешифрует данные, переданные в аргументе $data
         * @param string $data Значение для расшифровки
         *
         * @return string Расшифрованные данные
         */
        public static function decrypt($data)
        {
            return mcrypt_decrypt(MCRYPT_BLOWFISH, Config::get('secret_key'), $data, MCRYPT_MODE_ECB);
        }

        /**
         * Упаковывает строку в кодировку base64
         *
         * @param string $data Строка для упаковки
         *
         * @return string Упакованная строка
         */
        public static function base64_encode($data)
        {
            return base64_encode($data);
        }

        /**
         * Распаковывает строку из кодировки base64
         *
         * @param string $data Строка для распаковки
         *
         * @return string Распакованная строка
         */
        public static function base64_decode($data)
        {
            return base64_decode($data);
        }

        /**
         * Хэширует строку с применением алгоритма, определенного в аргументе $algo
         * @param string $data Строка для хэширования
         * @param string $algo Название алгоритма для хэширования
         *
         * @throws UnsupportedAlgorithmException Исключение, в случае отправки в качестве аргумента $algo
         * неподдерживаемый алгоритм хэширования
         *
         * @return string Хэшированная строка
         */
        public static function hash($data, $algo = 'md5')
        {
            if (in_array($algo, hash_algos())) {
                return hash($algo, $data);
            }
            throw new UnsupportedAlgorithmException('Алгоритм хэширования ' . $algo
                . ' не поддерживается на текущей системе!');
        }

    }
