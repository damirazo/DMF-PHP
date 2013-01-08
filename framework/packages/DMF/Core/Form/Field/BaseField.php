<?php

    namespace DMF\Core\Form\Field;

    use DMF\Core\Component\Component;
    use DMF\Core\Form\Exception\RuleNotFound;
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

        /** @var string Тип возвращаемого значения - массив или единственное значение */
        protected $type = 'single';

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
         * @throws RuleNotFound
         * @return \DMF\Core\Form\Validator
         */
        public function validate()
        {
            $value = $this->value();
            // обходим массив правил проверки поля
            foreach ($this->rules() as $rule_element) {
                $rule_segments = explode('=', $rule_element);
                $rule_name = $rule_segments[0];
                $rule_value = count($rule_segments) == 2 ? $rule_segments[1] : true;
                // если валидатор сообщил, что он невалиден,
                // то прерываем проверку поля
                if (!$this->validator->is_valid()) {
                    break;
                }
                // переменная для хранение текста ошибки
                $message = false;
                // вначале проверяем наличие метода в нужном формате в объекте формы
                // пример метода формы 'rule__username__min_length
                // где username - имя поля, min_length - имя правила
                if (method_exists($this->form, 'rule__' . $this->name . '__' . $rule_name)) {
                    $message = call_user_func_array(
                        [$this->form, 'rule__' . $this->name . '__' . $rule_name], [$value, $rule_value, $this]
                    );
                }
                // если правило формы не задано, то проверяем его в объекте поля
                // формат имени метода 'rule__min_length'
                // где min_length - имя правила
                elseif (method_exists($this, 'rule__' . $rule_name)) {
                    $message = call_user_func_array([$this, 'rule__' . $rule_name], [$value, $rule_value]);
                }
                else {
                    throw new RuleNotFound('Не задано правило ' . $rule_name . ' для проверки поля ' . $this->name);
                }
                // если в переменную был записан текст ошибки,
                // то записываем ее в валидатор и делаем его невалидным
                if ($message !== false) {
                    $this->validator->add_error($message);
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
            return $this->form->value($this->name(), $this->type == 'array' ? [] : '');
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
         * Возвращает значение правила с указанным именем
         * @param string $name Имя правила
         * @return bool
         */
        public function rule($name)
        {
            if (isset($this->rules()[$name])) {
                return $this->rules()[$name];
            }
            return false;
        }

        /**
         * Список дополнительных элементов полей
         * @return bool|array
         */
        public function options()
        {
            return false;
        }

        /**
         * Возвращает имя поля
         * @return null|string
         */
        public function name()
        {
            return $this->name;
        }

        /**
         * Возвращает информацию о том является ли поле обязательным
         * @return bool
         */
        public function is_required()
        {
            return $this->rule('required');
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
         * @param string  $name    Имя параметра
         * @param mixed   $default Значение по умолчанию
         * @return mixed
         */
        protected function data($name, $default = false)
        {
            if (isset($this->data[$name])) {
                return $this->data[$name];
            }
            return $default;
        }

        /**
         * Возвращает кастомное или дефолтное сообщение об ошибке валидации
         * @param string $rule_name       Имя правила валидации
         * @param        $default_message Сообщение об ошибке по умолчанию
         * @return mixed
         */
        protected function error_message($rule_name, $default_message)
        {
            if (isset($this->data['errors']) && isset($this->data['errors'][$rule_name])) {
                return $this->data['errors'][$rule_name];
            }
            return $default_message;
        }

        /**
         * Возвращает отформатированную строку, содержащую HTML атрибуты поля
         * @return string
         */
        protected function html_attrs()
        {
            $attrs = [];
            if (isset($this->data['attrs']) && is_array($this->data['attrs'])) {
                foreach ($this->data['attrs'] as $attr_name => $attr_value) {
                    $attrs[] = $attr_name . '="' . $attr_value . '"';
                }
            }
            return implode(' ', $attrs);
        }

        /** Проверка заполненности обязательного поля */
        protected function rule__required($value, $rule)
        {
            if (!$value || (is_string($value) && mb_strlen($value) == 0) || (is_array($value) && count($value) == 0)) {
                return sprintf(
                    $this->error_message('required', 'Поле "%s" обязательно для заполнения!'),
                    $this->label()
                );
            }
            return false;
        }

        /** Проверка минимальной длины значения */
        protected function rule__min_length($value, $rule)
        {
            if (is_string($value) && mb_strlen($value) < $rule) {
                return sprintf(
                    $this->error_message('min_length', 'Значение поля "%s" не может быть менее %d символов!'),
                    $this->label(), $rule
                );
            }
            return false;
        }

        /** Проверка максимальной длины значения */
        protected function rule__max_length($value, $rule)
        {
            if (is_string($value) && mb_strlen($value) > $rule) {
                return sprintf(
                    $this->error_message('max_length', 'Значение поля "%s" не может быть более %d символов!'),
                    $this->label(), $rule
                );
            }
            return false;
        }

        /** Проверка на соответствие значения регулярному выражению */
        protected function rule__pattern($value, $rule)
        {
            $modifiers = is_array($rule) ? $rule[1] : '';
            $pattern = is_array($rule) ? $rule[0] : $rule;
            if (!preg_match('~' . $pattern . '~' . $modifiers, $value)) {
                return sprintf(
                    $this->error_message('pattern', 'Значение поля "%s" не соответствует требуемому шаблону!'),
                    $this->label()
                );
            }
            return false;
        }

        /** Проверка наличия полученного значения/значений в списке допустимых */
        public function rule__check_value_in_list($value, $rule)
        {
            // массив допустимых значений данного поля
            $allowed_value = array_keys($this->data('options', []));
            // если полученное значение является массивом и число элементов больше нуля,
            // то обходим в цикле все его элементы и проверяем их наличие в массиве допустимых значений
            if (is_array($value) && count($value) > 0) {
                foreach ($value as $element) {
                    if (!in_array($element, $allowed_value) && count($value) > 0) {
                        return sprintf(
                            $this->error_message('check_value', 'Значение поля "%s" отсутствует в списке допустимых!'),
                            $this->label()
                        );
                    }
                }
            }
            // если полученное значение является строкой и она не пустая,
            // то проверяем ее наличие в списке допустимых значений
            elseif (is_string($value) && $value != '') {
                if (!in_array($value, $allowed_value) && count($value) > 0) {
                    return sprintf(
                        $this->error_message('check_value', 'Значение поля "%s" отсутствует в списке допустимых!'),
                        $this->label()
                    );
                }
            }
            return false;
        }

    }
