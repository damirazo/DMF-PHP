<?php

    namespace DMF\Core\Form\Field;

    /**
     * Поле для выборки даты и времени
     * На текущий момент корректно работает лишь с браузерами Opera, Chrome и Safari
     */
    class DatetimeField extends InputField
    {

        /** {@inheritdoc} */
        protected $type = 'datetime-local';

    }
