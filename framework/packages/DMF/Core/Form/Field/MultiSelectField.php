<?php

    namespace DMF\Core\Form\Field;

    /**
     * Выпадающий список с возможностью выбора нескольких значений
     */
    class MultiSelectField extends BaseField
    {

        /** {@inheritdoc} */
        protected $rules = ['check_value_in_list'];

        /** {@inheritdoc} */
        protected $type = 'array';

        /** {@inheritdoc} */
        public function html()
        {
            $options = $this->data('options', []);
            $data = [];
            $values = is_array($this->value()) ? $this->value() : [];
            foreach ($options as $option_name => $option_label) {
                $value = in_array($option_name, $values) ? 'selected' : '';
                $data[] = '<option value="' . $option_name . '" ' . $value . '>' . $option_label . '</option>';
            }
            return '<select multiple name="' . $this->name . '[]">' . implode('', $data) . '</select>';
        }

    }
