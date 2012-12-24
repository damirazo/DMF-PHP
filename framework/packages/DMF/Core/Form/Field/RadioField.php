<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * RadioField.php
     * 28.11.12, 17:20
     */

    namespace DMF\Core\Form\Field;

    /**
     * Поле для радио элементов формы
     */
    class RadioField extends BaseField
    {

        /**
         * {@inheritdoc}
         */
        public function _get_html_code($name, $default)
        {
            return $this->get_options($name, $default);
        }

        /**
         * {@inheritdoc}
         */
        protected function get_options($name, $default)
        {
            $data = [];
            $default_value = (is_string($default)) ? [$default] : $default;
            if ($this->_rules('options') && is_array($this->_rules('options'))) {
                $options = $this->_rules('options');
                foreach ($options as $option_name => $option_label) {
                    $value = (in_array($option_name, $default_value)) ? ' checked="checked" ' : '';
                    $data[] = [
                        'field' => '<input type="radio" name="' . $name . '" '
                            . $this->_params() . ' value="' . $option_name . '"' . $value . '>',
                        'label' => $option_label
                    ];
                }
            }

            return $data;
        }

        /**
         * {@inheritdoc}
         */
        public function validate($form, $value, $label)
        {
            /** @var $validator \DMF\Core\Form\Validator */
            $validator = parent::validate($form, $value, $label);
            if ($this->_rules('options') && is_array($this->_rules('options')) && !empty($value)) {
                $options = $this->_rules('options');
                if (!in_array($value, array_keys($options))) {
                    $validator->add_error('Получено неверное значение поля "' . $label . '"');
                }

            }

            return $validator;
        }

    }
