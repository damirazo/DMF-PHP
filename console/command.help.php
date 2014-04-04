<?php
    
    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    format('Список доступных команд:');

    foreach ($commands as $command_name => $command_info) {
        format('"%s": %s', $command_name, $command_info[1]);
    }
