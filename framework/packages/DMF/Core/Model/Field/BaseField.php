<?php

    namespace DMF\Core\Model\Field;

    use DMF\Core\Component\Component;

    /**
     * Базовая модель для поля в БД
     */
    abstract class BaseField extends Component
    {

        /**
         * @var array Массив параметров поля
         */
        protected $_params = [];

        /**
         * Инициализация поля
         * @param array $params Массив параметров
         */
        public function __construct($params = [])
        {
            $this->_params = $params;
        }

        /**
         * Создание поля в БД
         * @param string $name Название поля
         *
         * @return string
         */
        public function create_sql($name)
        {
            return '';
        }

        /**
         * Возвращает параметр с именем $name или значение $default
         * @param string $name    Имя параметра
         * @param bool   $default Значение по умолчанию
         *
         * @return bool
         */
        public function get_param($name, $default = false)
        {
            if (isset($this->_params[$name])) {
                return $this->_params[$name];
            }

            return $default;
        }

        /**
         * Возвращает хэш текущих настроек поля
         * @return string
         */
        public function __toString()
        {
            return md5(get_class($this).serialize($this->_params));
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
            return false;
        }

    }
