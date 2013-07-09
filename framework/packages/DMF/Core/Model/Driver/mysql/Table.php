<?php

    namespace DMF\Core\Model\Driver\mysql;

    use DMF\Core\Model\Driver\base\TableDriver;

    class Table extends TableDriver
    {

        /** Создание таблицы в БД */
        const CREATE_TABLE = 'CREATE TABLE IF NOT EXISTS :table_name (:fields) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        /** Удаление таблицы из БД */
        const DROP_TABLE = 'DROP TABLE IF EXISTS :table_name';
        /** Проверка наличия таблицы в БД */
        const EXISTS_TABLE = 'SHOW TABLES LIKE :table_name';

        /** Вставка новой записи в таблицу */
        const INSERT_RECORD = 'INSERT INTO :table_name SET :params';
        /** Обновление существующей записи */
        const UPDATE_RECORD = 'UPDATE :table_name SET :params';
        /** Выборка записи из таблицы */
        const SELECT_RECORD = 'SELECT :selected FROM :table_name';

        /** Формирование условия для запроса */
        const WHERE = 'WHERE :attrs';
        /** Формирование сортировки для запроса */
        const ORDER_BY = 'ORDER BY :field_name:direction';
        /** Формирование лимита для запроса */
        const LIMIT = 'LIMIT :count,:position';
        /** Формирование подсчета числа данных в запросе */
        const COUNT = 'COUNT(:values)';

    }