<?php

    namespace DMF\Core\Form\Field;

    /**
     * Поле с чекбоксами
     */
    class CheckboxField extends BaseField
    {

        /** {@inheritdoc} */
        protected $type = 'array';

        /** {@inheritdoc} */
        protected $rules = ['check_value_in_list'];

        /** {@inheritdoc} */
        public function html()
        {
            $data = [];
            foreach ($this->options() as $option) {
                $data[] = '<li>' . $option['field'] . ' ' . $option['label'] . '</li>';
            }
            return '<ul>' . implode('', $data) . '</ul>';
        }

        /**
         * Возвращает массив полей и их лейблов
         * @return array
         */
        public function options()
        {
            $options = $this->data('options', []);
            $data = [];
            foreach ($options as $option_value => $option_label) {
                $value = $this->value() == $option_value ? 'checked' : '';
                $data[] = [
                    'field' => '<input type="checkbox" name="' . $this->name
                            . '[]" value="' . $option_value . '" ' . $value . '>',
                    'label' => $option_label
                ];
            }
            return $data;
        }

        /** Проверка наличия полученного значения/значений в списке допустимых */
        public function rule__check_value($value, $rule)
        {
            // проверяем значение
            $allowed_value = array_keys($this->data('options', []));
            foreach ($value as $element) {
                if (!in_array($element, $allowed_value) && count($value) > 0) {
                    return sprintf(
                        $this->error_message('check_value', 'Значение поля "%s" отсутствует в списке допустимых!'),
                        $this->label()
                    );
                }
            }
            return false;
        }

    }
