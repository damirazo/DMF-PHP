<?php

    namespace DMF\DMFAuth\Event;

    use DMF\Core\Event\Event;
    use DMF\Core\Template\Context;
    use DMF\Core\Storage\Session;

    /**
     * Обработка событий пользователя
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
