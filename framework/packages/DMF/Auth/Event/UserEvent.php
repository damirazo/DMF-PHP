<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Auth\Event;

    use DMF\Core\Event\Event;
    use DMF\Core\Template\Context;
    use DMF\Core\Storage\Session;

    /**
     * Class UserEvent
     * Обработка пользовательских событий
     *
     * @package DMF\Auth\Event
     */
    class UserEvent extends Event
    {

        /**
         * Аутентификация пользователя
         */
        public function authenticate()
        {
            /** @var $user \DMF\Auth\Model\User $user */
            $user = $this->model('Auth.User')->authenticate();
            Context::add('user', $user);
        }

    }
