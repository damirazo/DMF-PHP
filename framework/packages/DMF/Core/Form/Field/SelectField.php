<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form\Field;

    /**
     * Class SelectField
     * Простой выпадающий список
     *
     * @package DMF\Core\Form\Field
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
