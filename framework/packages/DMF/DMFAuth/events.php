<?php

    use DMF\Core\Event\Event;

    /** Загрузка и инициализация событий */
    Event::add_listener('boot', 'DMFAuth.DMFUserEvent.authenticate');