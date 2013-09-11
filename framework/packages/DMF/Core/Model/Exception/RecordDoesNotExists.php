<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Exception;

    use DMF\Core\Application\Exception\BaseException;

    /**
     * Class RecordDoesNotExists
     * Выбрасывается при отсутствии требуемой записи В БД
     *
     * @package DMF\Core\Model\Exception
     */
    class RecordDoesNotExists extends BaseException
    {

        public function __construct(\DMF\Core\Model\Model $model_instance, $pk)
        {
            $error_message = sprintf(
                'Записи %s с id=%s не обнаружено в базе данных!',
                $model_instance->class_namespace(),
                $pk
            );
            parent::__construct($error_message);
        }

    }