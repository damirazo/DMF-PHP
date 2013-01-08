<?php

    namespace DMF\Core\Form;

    /**
     * Преобразование модели в форму
     */
    class ModelForm extends Form
    {

        /** @var string Имя связанной модели */
        public $model;

        /**
         * Заданные лейблы для имен полей
         * @return array
         */
        public function labels()
        {
            return [];
        }

        public function scheme()
        {
            $fields = [];
            /** @var $model \DMF\Core\Model\Model */
            $model = $this->model($this->model);
            /** @var $field_object \DMF\Core\Model\Field\BaseField */
            foreach ($model->_scheme() as $field_name => $field_object) {
                $field_type = $field_object->type();
                if ($field_type == 'primary_key') {
                    continue;
                }
                $fields[$field_name] = $this->get_form_field_form_model_field($field_name, $field_object);
            }
            return $fields;
        }

        /**
         * Возвращает сгенерированное имя поля из заданного массива
         * или на основе имени поля в модели
         * @param $name
         * @return string
         */
        protected function get_field_label($name)
        {
            // если имя для поля было указано, то возвращаем его
            if (isset($this->labels()[$name])) {
                return $this->labels()[$name];
            }
            // в противном случае генерируем какое-нибудь
            $words = explode('_', $name);
            return ucwords(strtolower(implode(' ', $words)));
        }

        /**
         * Возвращает поле формы, соответствующее данному полю модели
         * @param string                          $name Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object
         * @return array
         */
        protected function get_form_field_form_model_field($name, \DMF\Core\Model\Field\BaseField $object)
        {
            switch ($object->type()) {
                case 'string':
                    return $this->convert_string_field($name, $object);
                case 'text':
                    return $this->convert_text_field($name, $object);
                case 'datetime':
                    return $this->convert_datetime_field($name, $object);
                case 'boolean':
                    return $this->convert_string_field($name, $object);
                default:
                    return $this->convert_string_field($name, $object);
            }
        }

        /**
         * Генерация обычного текстового поля
         * @param string                          $name Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object
         * @return array
         */
        protected function convert_string_field($name, \DMF\Core\Model\Field\BaseField $object)
        {
            return [
                'type'  => 'DMF.InputField',
                'label' => $this->get_field_label($name),
                'rules' => ['max_length' => $object->length()]
            ];
        }

        /**
         * Генерация расширенного текстового поля
         * @param string                          $name Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object
         * @return array
         */
        protected function convert_text_field($name, \DMF\Core\Model\Field\BaseField $object)
        {
            return [
                'type'  => 'DMF.TextField',
                'label' => $this->get_field_label($name)
            ];
        }

        /**
         * Генерация расширенного текстового поля
         * @param string                          $name Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object
         * @return array
         */
        protected function convert_datetime_field($name, \DMF\Core\Model\Field\BaseField $object)
        {
            return [
                'type'  => 'DMF.DatetimeField',
                'label' => $this->get_field_label($name)
            ];
        }

    }
