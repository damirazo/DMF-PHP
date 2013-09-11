<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Form\Field;

    /**
     * Class TextField
     * Многострочное текстовое поле
     *
     * @package DMF\Core\Form\Field
     */
    class TextField extends BaseField
    {

        /** {@inheritdoc} */
        public function html()
        {
            return '<textarea name="' . $this->name . '" ' . $this->html_attrs()
                    . '>' . $this->value() . '</textarea>';
        }

    }
