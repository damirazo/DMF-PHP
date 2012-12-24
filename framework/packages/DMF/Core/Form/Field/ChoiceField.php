<?php

    /**
     * Этот файл часть фреймворка DM Framework
     *
     * (c) damirazo <damirazo.kazan@gmail.com> 2012
     * ChoiceField.php
     * 28.11.12, 15:06
     */

    namespace DMF\Core\Form\Field;

    /**
     * Поле для выборки множественных значений из списка
     */
    class ChoiceField extends BaseField
    {

        /**
         * {@inheritdoc}
         */
        public function _get_html_code($name, $default)
        {
            return '<select name="' . $name . '" ' . $this->_params() . '>' . $this->get_options(
                $default
            ) . '</select>';
        }

        /**
         * Возвращает HTML код для выбираемых из списка значений
         *
         * @param string $default Имя параметра по умолчанию
         *
         * @return string
         */
        protected function get_options($default)
        {
            $data = [];
            if ($this->_rules('options') && is_array($this->_rules('options'))) {
                $options = $this->_rules('options');
                foreach ($options as $option_name => $option_label) {
                    $value = ($option_name == $default) ? ' selected="selected" ' : '';
                    $data[] = '<option value="' . $option_name . '"' . $value . '>' . $option_label . '</option>';
                }
            }

            return implode('', $data);
        }

        /**
         * {@inheritdoc}
         */
        public function _defaults()
        {
            return [];
        }

        /**
         * {@inheritdoc}
         */
        public function validate($form, $value, $label)
        {
            /** @var $validator \DMF\Core\Form\Validator */
            $validator = parent::validate($form, $value, $label);
            if ($this->_rules('options') && is_array($this->_rules('options'))) {
                $options = $this->_rules('options');
                if (!in_array($value, array_keys($options))) {
                    $validator->add_error('Получено неверное значение поля "' . $label . '"');
                }
            }
            return $validator;
        }

    }
