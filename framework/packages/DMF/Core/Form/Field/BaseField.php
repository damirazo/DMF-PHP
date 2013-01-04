<?php

    namespace DMF\Core\Form\Field;

    use DMF\Core\Component\Component;
    use DMF\Core\Form\Validator;

    /**
     * Базовый объект поля
     */
    abstract class BaseField extends Component
    {

        /** @var \DMF\Core\Form\Form|null Объект формы */
        protected $form = null;

        /** @var array Настройки поля */
        protected $data = [];

        /** @var null|string Имя поля */
        protected $name = null;

        /** @var null|\DMF\Core\Form\Validator */
        protected $validator = null;

        /** @var null|mixed Значение поля */
        protected $value = null;

        /** @var array Массив правил для проверки поля */
        protected $rules = [];

        /**
         * Конструктор поля
         * @param \DMF\Core\Form\Form $form Объект формы
         * @param string              $name Имя поля
         * @param array               $data Настройки поля
         */
        public function __construct($form, $name, $data)
        {
            $this->form = $form;
            $this->data = $data;
            $this->name = $name;
            $this->validator = new Validator();
        }

        /**
         * Возвращает лейбл поля
         * @return null|string
         */
        public function label()
        {
            return !(isset($this->data['label'])) ? $this->generate_label() : $this->data['label'];
        }

        /**
         * Возвращает HTML код для отображения поля
         * @return string
         */
        public function html()
        {
            return '';
        }

        /**
         * Валидация значения, переданного полю
         * @param mixed               $value Переданное значение
         * @return \DMF\Core\Form\Validator
         */
        public function validate($value)
        {
            // обходим массив правил проверки поля
            foreach ($this->rules() as $rule_name => $rule_value) {
                // если валидатор сообщил, что он невалиден,
                // то прерываем проверку поля
                if (!$this->validator->is_valid()) {
                    break;
                }
                // проверяем наличия правила с нужным именем
                // и вызываем его
                if (method_exists($this, 'rule__' . $rule_name)) {
                    call_user_func_array([$this, 'rule__' . $rule_name], [$value, $rule_value]);
                }
            }
            // возвращаем "заряженный" объект валидатора
            return $this->validator;
        }

        /**
         * Получение сообщения об ошибке от валидатора
         * @return null|string
         */
        public function error()
        {
            return $this->validator->get_error();
        }

        /**
         * Возвращает значение поля
         * @return mixed
         */
        public function value()
        {
            return $this->form->value($this->name);
        }

        /**
         * Возвращает массив правил для проверки поля
         * @return array
         */
        public function rules()
        {
            return array_merge(isset($this->data['rules']) ? $this->data['rules'] : [], $this->rules);
        }

        /**
         * Генерация лейбла поля из имени класса
         * @return string
         */
        protected function generate_label()
        {
            return ucfirst(strtolower($this->get_class_name()));
        }

        /**
         * Возвращает параметр с требуемым именем или значение по умолчанию
         * @param string $name    Имя параметра
         * @param bool   $default Значение по умолчанию
         * @return bool
         */
        protected function data($name, $default = false)
        {
            if (isset($this->data[$name])) {
                return $this->data[$name];
            }
            return $default;
        }

        /**
         * Правило для проверки обязательных для заполнения полей
         * @param mixed $value Значение
         * @param mixed $rule  Параметры правила
         */
        protected function rule__required($value, $rule)
        {
            if (is_string($value) && strlen($value) == 0) {
                $this->validator->add_error('Поле "' . $this->label() . '" обязательно для заполнения!');
            }
            elseif (is_array($value) && count($value) == 0) {
                $this->validator->add_error(
                    'Необходимо выбрать не менее одного значения поля "' . $this->label() . '"!'
                );
            }
        }

        /**
         * Правило для проверки минимальной длины значения
         * @param mixed $value Значение
         * @param mixed $rule  Параметры правила
         */
        protected function rule__min_length($value, $rule)
        {
            if (is_string($value) && mb_strlen($value) < $rule) {
                $this->validator->add_error(
                    'Значение поля "' . $this->label() . '" не может быть менее ' . $rule . ' символов!'
                );
            }
        }

        /**
         * Правило для проверки максимальной длины значения
         * @param mixed $value Значение
         * @param mixed $rule  Параметры правила
         */
        protected function rule__max_length($value, $rule)
        {
            if (is_string($value) && mb_strlen($value) > $rule) {
                $this->validator->add_error(
                    'Значение поля "' . $this->label() . '" не может быть более ' . $rule . ' символов!'
                );
            }
        }

        /**
         * Правило для проверки соответствия значения регулярному выражению
         * @param mixed $value Значение
         * @param mixed $rule  Параметры правила
         */
        protected function rule__pattern($value, $rule)
        {

        }

    }
