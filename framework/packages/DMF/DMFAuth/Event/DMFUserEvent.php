<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\DMFAuth\Event;

    use DMF\Core\Event\Event;
    use DMF\Core\Template\Context;
    use DMF\Core\Storage\Session;

    /**
     * Class DMFUserEvent
     * Обработка пользовательских событий
     *
     * @package DMF\DMFAuth\Event
     */
    class DMFUserEvent extends Event
    {

        /**
         * Аутентификация пользователя
         */
        public function authenticate()
        {
            $user = $this->model('DMFAuth.DMFUser')->authenticate();
            Context::add('user', $user);
        }

    }
