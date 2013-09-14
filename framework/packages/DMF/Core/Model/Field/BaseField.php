<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Model\Field;

    use DMF\Core\Component\Component;

    /**
     * Class BaseField
     * Базовое описание поле модели в БД
     *
     * @package DMF\Core\Model\Field
     */
    abstract class BaseField extends Component
    {

        /** @var array Массив параметров поля */
        public $params = [];

        /**
         * Инициализация поля
         * @param array $params Массив параметров
         */
        public function __construct($params = [])
        {
            $this->params = $params;
        }

        /**
         * Создание поля в БД
         * @param string $name Название поля
         * @return string
         */
        public function sql($name)
        {
            return sprintf('`%s` %s %s %s',
                $name,
                $this->sql_type(),
                $this->sql_nullable(),
                $this->sql_default()
            );
        }

        /**
         * Возвращает параметр с именем $name или значение $default
         * @param string          $name    Имя параметра
         * @param bool|null|mixed $default Значение по умолчанию
         * @return bool
         */
        public function get_param($name, $default = null)
        {
            if (isset($this->params[$name])) {
                return $this->params[$name];
            }
            return $default;
        }

        /**
         * Является ли указанное поле обязательным для заполнения
         * В случае, если значение поля содержит значение по умолчанию - поле является необязательным для заполнения,
         * т.к. в данном случае в пустое поле будет подставлено значение по умолчанию
         * @return bool
         */
        public function is_required()
        {
            return $this->get_param('required', false) && is_null($this->get_param('default'));
        }

        /**
         * Возвращает хэш текущих настроек поля
         * @param string $name Название таблицы в БД
         * @return string
         */
        public function hash($name)
        {
            return md5($name . '+' . serialize($this->params));
        }

        /**
         * Возвращает тип поля
         * @return string
         */
        public function type()
        {
            return 'base_field';
        }

        /**
         * Возвращает длину поля
         * @return int
         */
        public function length()
        {
            return 0;
        }

        /**
         * Возвращает значение по умолчанию
         * @return mixed
         */
        public function default_value()
        {
            return $this->get_param('default');
        }

        /**
         * Формирование части SQL запроса со значением по умолчанию
         *
         * @return string
         */
        public function sql_default()
        {
            return !is_null($this->get_param('default')) ? 'DEFAULT "' . $this->default_value() . '"' : '';
        }

        /**
         * Формирование части SQL запроса с информацией о возможности хранения нулевого значения
         *
         * @return string
         */
        public function sql_nullable()
        {
            return !$this->get_param('nullable') ? 'NOT NULL ' : 'NULL';
        }

        /**
         * Формирование части SQL запроса с типом данного поля
         *
         * @return string
         */
        public function sql_type()
        {
            return 'VARCHAR(255)';
        }

    }
