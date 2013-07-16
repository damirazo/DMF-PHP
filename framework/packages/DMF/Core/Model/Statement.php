<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model;

    /**
     * Class Statement
     * Обработка ответов и состояния БД
     *
     * @package DMF\Core\Model
     */
    class Statement
    {

        /** @var null|\PDOStatement Объект запроса */
        protected $_statement = null;

        /** @var null|\PDO Курсор БД */
        protected $_db = null;

        /** Инициализация объекта */
        public function __construct(\PDO $db, \PDOStatement $statement)
        {
            $this->_statement = $statement;
            $this->_db = $db;
        }

        /** Возвращает один объект */
        public function fetch_one()
        {
            return $this->_statement->fetch();
        }

        /** Возвращает все объекты */
        public function fetch_all()
        {
            return $this->_statement->fetchAll();
        }

        /** Выполнение запроса */
        public function send()
        {
            return $this->_statement->execute();
        }

        /** Возвращает число объектов */
        public function num_rows()
        {
            return $this->_statement->rowCount();
        }

        /** Возвращает ID последней добавленной записи */
        public function last_insert_id()
        {
            return $this->_db->lastInsertId();
        }

    }
