<?php

    use DMF\Core\Event\Event;

    /** Загрузка и инициализация событий */
    Event::on('boot', 'DMFAuth.DMFUserEvent.authenticate');