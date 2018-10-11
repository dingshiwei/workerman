<?php

register_shutdown_function('my_shutdown_handler');

function my_shutdown_handler()
{
    $error = error_get_last();
    if ($error) {
        if (in_array($error['type'], array(E_WARNING, E_NOTICE))) {
            return;
        }
//        my_log('logs', $error, $_SERVER, $_POST);
    }

    return false;
}
