<?php

    namespace DMF\Core\Form\Field;

    /**
     * Выпадающий список значений
     */
    class SelectField extends BaseField
    {

        /** {@inheritdoc} */
        protected $rules = ['check_value_in_list'];

        /** {@inheritdoc} */
        public function html()
        {
            $options = $this->data('options', []);
            $data = [];
            foreach ($options as $option_name => $option_label) {
                $value = $option_name == $this->value() ? 'selected' : '';
                $data[] = '<option value="' . $option_name . '" ' . $value . '>' . $option_label . '</option>';
            }
            return '<select name="' . $this->name . '">' . implode('', $data) . '</select>';
        }

    }
