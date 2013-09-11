<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form\Field;

    /**
     * Class DatetimeField
     * Поле для выборки значения даты
     * Использует HTML5 теги
     *
     * @package DMF\Core\Form\Field
     */
    class DatetimeField extends InputField
    {

        /** {@inheritdoc} */
        protected $type = 'datetime-local';

    }
