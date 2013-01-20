<?php

    /*
     * Объявления "прослушивания" событий
     */
    
    use DMF\Core\Event\Event;

    /** Загрузка и инициализация событий */
    Event::add_listener('boot', 'System.on_boot');