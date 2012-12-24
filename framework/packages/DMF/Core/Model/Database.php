<?php

    namespace DMF\Core\Model;

    use DMF\Core\Storage\Config;

    /**
     * Класс-обертка для работы с БД
     */
    class Database extends \PDO
    {

        /**
         * Инициализация подключения
         */
        public function __construct()
        {
            $db_config = Config::get('database');
            if ($db_config['enable']) {
                $dsn = 'mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['name'];
                parent::__construct($dsn, $db_config['user'], $db_config['password']);
                $this->exec('SET NAMES utf8');
            }
        }

        /**
         * Осуществление запроса к БД
         * @param string $query  Код запроса
         * @param array  $params Массив параметров
         *
         * @return Statement|\PDOStatement
         * @throws \Exception
         */
        public function query($query, $params = [])
        {
            /**
             * @var \PDOStatement $q
             */
            $q = $this->prepare($query);
            $q->setFetchMode(\PDO::FETCH_ASSOC);
            $q->execute($params);

            $error = $q->errorInfo();
            if ($error[0] != '0000') {
                throw new \Exception('[DB] ' . $error[2]);
            }

            return new Statement($this, $q);
        }

    }
