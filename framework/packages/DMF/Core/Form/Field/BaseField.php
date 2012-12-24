<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * BaseField.php
     * 27.11.12, 17:32
     */

    namespace DMF\Core\Form\Field;

    use DMF\Core\Component\Component;
    use DMF\Core\Http\Request;
    use DMF\Core\Form\Validator;

    /**
     * Класс базового поля в форме
     */
    class BaseField extends Component
    {

        /** @var array Список параметров поля */
        protected $_params = [];

        /** @var array Список правил валидации */
        protected $_rules = [];

        /** @var string Имя поля */
        public $label = '';

        /** @var \DMF\Core\Form\Validator|null Объект валидатора */
        public $validator = null;

        /**
         * Инициализация поля
         * @param string $label  Имя поля
         * @param array  $params Список параметров
         * @param array  $rules  Список правил валидации
         */
        public function __construct($label, $rules = [], $params = [])
        {
            $this->_params = $params;
            $this->_rules = $rules;
            $this->label = $label;
            if (is_null($this->validator)) {
                $this->validator = new Validator();
            }
        }

        /**
         * Возвращает HTML код поля
         *
         * @param string $name    Имя поля
         * @param mixed  $default Значение поля по умолчанию
         *
         * @return string
         */
        public function _get_html_code($name, $default)
        {
            return '&nbsp;';
        }

        /**
         * Значения полей по умолчанию
         * @return array
         */
        public function _defaults()
        {
            return [];
        }

        /**
         * Список параметров поля
         * @return string
         */
        public function _params()
        {
            $data = [];
            $params = array_merge($this->_defaults(), $this->_params);
            foreach ($params as $param_name => $param_value) {
                $data[] = $param_name . '="' . $param_value . '"';
            }

            return implode(' ', $data);
        }

        /**
         * Возвращает правило с требуемым именем
         * @param string $name Имя правила
         *
         * @return bool|array
         */
        public function _rules($name)
        {
            if (isset($this->_rules[$name])) {
                return $this->_rules[$name];
            }

            return false;
        }

        /**
         * Валидация поля
         * @param \DMF\Core\Form\Form Объект формы
         * @param string $value Значение для валидации
         * @param string $label Имя поля
         *
         * @return array
         */
        public function validate($form, $value, $label)
        {
            /** Проверка обязательных полей */
            if ($this->_rules('required')) {
                /** Проверка для полей с одним значением */
                if (is_string($value) && mb_strlen($value) == 0) {
                    $this->validator->add_error('Поле "' . $label . '" обязательно для заполнения!');
                    /** Проверка для полей с несколькими значениями */
                } elseif (is_array($value) && count($value) == 0) {
                    $this->validator->add_error('В поле "' . $label . '" нужно выбрать хотя бы одно значение!');
                }
            }
            /** Проверка минимальной длины значения */
            if ($this->_rules('min_length') && mb_strlen($value) < $this->_rules('min_length')) {
                $this->validator->add_error(
                    'Значение поля "' . $label . '" должно быть больше '
                        . $this->_rules('min_length') . ' символов!'
                );
            }
            /** Проверка максимальной длины значения */
            if ($this->_rules('max_length') && mb_strlen($value) > $this->_rules('max_length')) {
                $this->validator->add_error(
                    'Значение поля "' . $label . '" должно быть меньше '
                        . $this->_rules('max_length') . ' символов!'
                );
            }
            /** Обработка кастомных валидаторов */
            if ($this->_rules('custom_rules')) {
                /** Если указан лишь один валидатор в виде строки */
                if (is_string($this->_rules('custom_rules'))) {
                    $data = call_user_func_array([$form, $this->_rules('custom_rules')], [$form, $value, $label]);
                    if (!$data['status']) {
                        $this->validator->add_error($data['message']);
                    }
                    /** Если указан массив валидаторов */
                } elseif (is_array($this->_rules('custom_rules'))) {
                    $rules = $this->_rules('custom_rules');
                    foreach ($rules as $rule) {
                        $data = call_user_func_array([$form, $rule], [$form, $value, $label]);
                        if (!$data['status']) {
                            $this->validator->add_error($data['message']);
                        }
                    }
                }
            }
            /** Проверка значения по регулярному выражению */
            if ($this->_rules('pattern')) {
                /** Если правило содержит массив, значит первое значение это регулярное выражение,
                 * а второе - модификаторы */
                if (is_array($this->_rules('pattern'))) {
                    $regexp = $this->_rules('pattern')[0];
                    $modificators = $this->_rules('pattern')[1];
                } /** В противном случае значение содержит регулярное выражение */
                else {
                    $regexp = $this->_rules('pattern');
                    $modificators = '';
                }
                if (preg_match('~' . $regexp . '~' . $modificators, $value) == 0) {
                    $this->validator->add_error('Значение поля "' . $label . '" не соответствует требуемому шаблону!');
                }
            }

            return $this->validator;
        }

        /**
         * Возвращает значение данного поля
         * @param string $name Имя поля
         * @param bool   $raw  Необходимо ли очищать значение
         *
         * @return bool|string
         */
        public function get($name, $raw = true)
        {
            if ($raw) {
                return $this->request()->REQUEST($name);
            } else {
                $value = $this->request()->REQUEST($name);

                return $this->clean($value);
            }
        }

    }
