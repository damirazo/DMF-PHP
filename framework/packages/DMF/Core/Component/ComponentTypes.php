<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Component;

    /**
     * Class ComponentTypes
     * Перечисление для возможных вариантов типов компонентов
     *
     * @package DMF\Core\Component
     */
    class ComponentTypes
    {

        /** Модели */
        const Model = 'Model';
        /** Формы */
        const Form = 'Form';
        /** Контроллеры */
        const Controller = 'Controller';
        /** События */
        const Event = 'Event';

    }