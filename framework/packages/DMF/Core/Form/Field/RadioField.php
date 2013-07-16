<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form\Field;

    /**
     * Class RadioField
     * Поле с радио-кнопкой
     *
     * @package DMF\Core\Form\Field
     */
    class RadioField extends BaseField
    {

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
                $value = $this->value() == $option_value ? 'checked="checked"' : '';
                $data[] = [
                    'field' => '<input type="radio" name="' . $this->name
                            . '" value="' . $option_value . '" ' . $value . '>',
                    'label' => $option_label
                ];
            }
            return $data;
        }

    }
