<?php

    namespace DMF\Core\Form;

    use DMF\Core\Form\Field\InputField;
    use DMF\Core\Form\Field\TextField;
    use DMF\Core\Form\Field\CheckboxField;
    use DMF\Core\Form\Field\RadioField;

    /**
     * Объект формы, генерируемой на основе модели
     */
    class ModelForm extends Form
    {

        /** @var \DMF\Core\Model\Model Экземпляр модели */
        public $model_name;

        /**
         * Возвращает заданные имена для полей
         * @return array
         */
        public function labels()
        {
            return [];
        }

        /**
         * Возвращает экземпляр объекта модели
         * @return \DMF\Core\Model\Model
         */
        public function get_model()
        {
            return $this->model($this->model_name);
        }

        /**
         * Возвращает схему модели
         * @return array
         */
        public function get_model_scheme()
        {
            return $this->get_model()->_scheme();
        }

        /**
         * Динамическая генерация схемы БД
         * @return array
         */
        public function scheme()
        {
            $fields = [];
            /** @var \DMF\Core\Model\Field\BaseField $field_object */
            foreach ($this->get_model_scheme() as $field_name => $field_object) {
                if ($field_object->type() == 'primary_key') {
                    continue;
                }
                $fields[$field_name] = $this->_get_form_field_by_model($field_name, $field_object);
            }

            return $fields;
        }

        /**
         * Возвращает нужный тип формы из поля модели
         * @param string $field_name   Имя поля
         * @param /DMF/Core/Model/Field/BaseField $field_object Объект поля
         * @return Field\CheckboxField|Field\InputField|Field\TextField
         */
        protected function _get_form_field_by_model($field_name, $field_object)
        {
            $field_type = $field_object->type();
            switch ($field_type) {
                case 'string':
                    return $this->_convert_string_field($field_name, $field_object);
                case 'text':
                    return $this->_convert_text_field($field_name, $field_object);
                case 'boolean':
                    return $this->_convert_boolean_field($field_name, $field_object);
                case 'timestamp':
                    return $this->_convert_timestamp_field($field_name, $field_object);
                default:
                    return $this->_convert_string_field($field_name, $field_object);
            }
        }

        /**
         * Генерация имени поля
         * @param string $field_name Имя поля
         *
         * @return string
         */
        protected function _get_field_label($field_name)
        {
            if (isset($this->labels()[$field_name])) {
                return $this->labels()[$field_name];
            } else {
                return ucwords(implode(' ', explode('_', $field_name)));
            }
        }

        /**
         * Генерация поля формы для строкового поля
         * @param string                         $field_name   Имя поля
         * @param \DMF\Core\Form\Field\BaseField $field_object Объект поля
         *
         * @return Field\InputField
         */
        protected function _convert_string_field($field_name, $field_object)
        {
            $value_length = $field_object->length();
            $value_default = $field_object->default_value();
            $label = $this->_get_field_label($field_name);

            return new InputField($label, [
                'required'   => true,
                'max_length' => $value_length
            ]);
        }

        /**
         * Генерация поля формы для текстового поля
         * @param string                         $field_name   Имя поля
         * @param \DMF\Core\Form\Field\BaseField $field_object Объект поля
         *
         * @return Field\TextField
         */
        protected function _convert_text_field($field_name, $field_object)
        {
            $label = $this->_get_field_label($field_name);

            return new TextField($label);
        }

        /**
         * Генерация поля формы для булева поля
         * @param string                         $field_name   Имя поля
         * @param \DMF\Core\Form\Field\BaseField $field_object Объект поля
         *
         * @return Field\CheckboxField
         */
        protected function _convert_boolean_field($field_name, $field_object)
        {
            $label = $this->_get_field_label($field_name);

            return new RadioField($label, [
                'options' => [
                    '0' => 'Отключить',
                    '1' => 'Включить'
                ]
            ]);
        }

        /**
         * Генерация поля формы для датавремени
         * @param string                         $field_name   Имя поля
         * @param \DMF\Core\Form\Field\BaseField $field_object Объект поля
         *
         * @return Field\InputField
         */
        protected function _convert_timestamp_field($field_name, $field_object)
        {
            $label = $this->_get_field_label($field_name);

            return new InputField($label, [
                    'required' => !($field_object->default_value())
                ]
            );
        }

    }
