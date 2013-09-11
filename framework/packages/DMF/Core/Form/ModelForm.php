<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form;

    /**
     * Class ModelForm
     * Базовый класс для модельной формы
     * Позволяет генерировать форму создания объекта,
     * используя схему указанной модели
     *
     * @package DMF\Core\Form
     */
    class ModelForm extends Form
    {

        /** @var string Имя связанной модели */
        public $model;
        /** @var array Список правил для полей модельной формы */
        public $rules = [];

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
            foreach ($model->scheme() as $field_name => $field_object) {
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
         * Массив имен исключаемых полей
         * @return array
         */
        public function excluded_fields()
        {
            return [];
        }

        /**
         * Возвращает поле формы, соответствующее данному полю модели
         * @param string                          $name   Имя поля
         * @param \DMF\Core\Model\Field\BaseField $field  Объект поля модели
         * @return array
         */
        protected function get_form_field_form_model_field($name, \DMF\Core\Model\Field\BaseField $field)
        {
            // если у поля указано значение по умолчанию,
            // то записываем его в поле
            if ($field->default_value() && !isset($this->bounded_data[$name])) {
                $this->bounded_data[$name] = $field->default_value();
            }
            // ищем требуемый тип поля модели и конвертируем в соответствующий тип поля формы
            switch ($field->type()) {
                // обычное текстовое поле
                case 'string':
                    $data = $this->convert_string_field();
                    break;
                // большое текстовое поле
                case 'text':
                    $data = $this->convert_text_field();
                    break;
                // поле ввода даты и времени
                case 'datetime':
                    $data = $this->convert_datetime_field();
                    break;
                // поле ввода булева значения
                case 'boolean':
                    $data = $this->convert_boolean_field();
                    break;
                // по умолчанию считаем обычным текстовым полем
                default:
                    $data = $this->convert_string_field();
                    break;
            }

            $data['label'] = $this->get_field_label($name);
            $data['rules'] = $this->get_rule($name);

            return $data;
        }

        /**
         * Генерация обычного текстового поля
         * @return array
         */
        protected function convert_string_field()
        {
            return [
                'type'  => 'DMF.InputField'
            ];
        }

        /**
         * Генерация расширенного текстового поля
         * @return array
         */
        protected function convert_text_field()
        {
            return [
                'type' => 'DMF.TextField'
            ];
        }

        /**
         * Генерация расширенного текстового поля
         * @return array
         */
        protected function convert_datetime_field()
        {
            return [
                'type' => 'DMF.DatetimeField'
            ];
        }

        /**
         * Генерация поля с булевым значением
         * @return array
         */
        protected function convert_boolean_field()
        {
            return [
                'type'    => 'DMF.RadioField',
                'options' => [
                    1 => 'Включено',
                    0 => 'Выключено'
                ]
            ];
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
         * Заданные лейблы для имен полей
         * @return array
         */
        public function labels()
        {
            return [];
        }

        /**
         * Возвращает список правил, определенных для указанного поля
         * @param string $field_name Название поля
         * @return bool|mixed
         */
        public function get_rule($field_name)
        {
            if (isset($this->rules[$field_name])) {
                return $this->rules[$field_name];
            }
            return [];
        }

    }
