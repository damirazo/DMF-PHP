<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form\Field;

    /**
     * Class PasswordField
     * Поле ввода пароля
     *
     * @package DMF\Core\Form\Field
     */
    class PasswordField extends InputField
    {

        /** {@inheritdoc} */
        protected $type = 'password';

    }
