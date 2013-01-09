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

        /**
         * Массив имен исключаемых полей
         * @return array
         */
        public function excluded_fields()
        {
            return [];
        }

        /**
         * Генерация схемы формы
         * @return array
         */
        public function scheme()
        {
            $fields = [];
            /** @var $model \DMF\Core\Model\Model */
            $model = $this->model($this->model);
            /** @var $field_object \DMF\Core\Model\Field\BaseField */
            foreach ($model->_scheme() as $field_name => $field_object) {
                // тип поля модели
                $field_type = $field_object->type();
                // если данное поле модели является первичным ключем
                // или указано в списке исключаемых полей,
                // то игнорируем его
                if ($field_type == 'primary_key' || in_array($field_name, $this->excluded_fields())) {
                    continue;
                }
                // генерируем необходимые данные для описания поля формы
                $fields[$field_name] = $this->get_form_field_form_model_field($field_name, $field_object);
            }
            return $fields;
        }

        /**
         * Возвращает сгенерированное имя поля из заданного массива
         * или на основе имени поля в модели
         * @param $name Имя поля
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
         * @param string                          $name   Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object Объект поля модели
         * @return array
         */
        protected function get_form_field_form_model_field($name, \DMF\Core\Model\Field\BaseField $object)
        {
            // если у поля указано значение по умолчанию,
            // то записываем его в поле
            if ($object->default_value() && !isset($this->bounded_data[$name])) {
                $this->bounded_data[$name] = $object->default_value();
            }
            // ищем требуемый тип поля модели и конвертируем в соответствующий тип поля формы
            switch ($object->type()) {
                // обычное текстовое поле
                case 'string':
                    return $this->convert_string_field($name, $object);
                // большое текстовое поле
                case 'text':
                    return $this->convert_text_field($name, $object);
                // поле ввода даты и времени
                case 'datetime':
                    return $this->convert_datetime_field($name, $object);
                // поле ввода булева значения
                case 'boolean':
                    return $this->convert_boolean_field($name, $object);
                // по умолчанию считаем обычным текстовым полем
                default:
                    return $this->convert_string_field($name, $object);
            }
        }

        /**
         * Генерация обычного текстового поля
         * @param string                          $name   Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object Объект поля модели
         * @return array
         */
        protected function convert_string_field($name, \DMF\Core\Model\Field\BaseField $object)
        {
            return [
                'type'  => 'DMF.InputField',
                'label' => $this->get_field_label($name),
                'rules' => ['max_length=' . $object->length()]
            ];
        }

        /**
         * Генерация расширенного текстового поля
         * @param string                          $name   Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object Объект поля модели
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
         * @param string                          $name   Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object Объект поля модели
         * @return array
         */
        protected function convert_datetime_field($name, \DMF\Core\Model\Field\BaseField $object)
        {
            return [
                'type'  => 'DMF.DatetimeField',
                'label' => $this->get_field_label($name)
            ];
        }

        /**
         * Генерация поля с булевым значением
         * @param                                 $name   Имя поля
         * @param \DMF\Core\Model\Field\BaseField $object Объект поля модели
         * @return array
         */
        protected function convert_boolean_field($name, \DMF\Core\Model\Field\BaseField $object)
        {
            return [
                'type'    => 'DMF.RadioField',
                'label'   => $this->get_field_label($name),
                'options' => [
                    1 => 'Включено',
                    0 => 'Выключено'
                ]
            ];
        }

    }
