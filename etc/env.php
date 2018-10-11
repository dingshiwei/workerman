<?php
/**
 *
 * User: Tricolor
 * DateTime: 2017/10/18 16:03
 */
if (isset($_SERVER) && !isset($_SERVER['CI_ENV']) && isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
    foreach ($_SERVER['argv'] as $arg) {
        if (!$arg) continue;
        if ($arg[0] === '-') continue;
        if (strtolower($arg) === 'test') {
            $_SERVER['CI_ENV'] = 'development';
            break;
        }
        if (strtolower($arg) === 'beta') {
            $_SERVER['CI_ENV'] = 'development_beta';
            break;
        }
    }
}
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');

if ( ! function_exists('dir_up_to')) {
    function dir_up($to = DIRECTORY_SEPARATOR)
    {
        if (!$to || $to === DIRECTORY_SEPARATOR) return __DIR__;
        $dir = rtrim($to, DIRECTORY_SEPARATOR);
        $here = dirname(__FILE__);
        while ($here && substr($here, -strlen($dir)) !== $dir) {
            if ($here === dirname($here)) break;
            $here = dirname($here);
        }
        return $here;
    }
}

if (!function_exists('set_error_display')) {
    function set_error_display()
    {
        switch (ENVIRONMENT) {
            case 'development':
                error_reporting(E_ALL & ~E_NOTICE);
                ini_set('display_errors', 0);
                break;
            case 'development_beta':
                error_reporting(E_ALL & ~E_NOTICE);
                ini_set('display_errors', 1);
                break;
            case 'production':
                ini_set('display_errors', 0);
                if (version_compare(PHP_VERSION, '5.3', '>=')) {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                } else {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
                }
                break;
            default:
                echo 'The application environment is not set correctly.';
                exit(1); // EXIT_ERROR
        }
    }
}

define('BASEPATH', dir_up('websocket') . DIRECTORY_SEPARATOR);
define('SELLERAPPPATH', dirname(dir_up('websocket')) . DIRECTORY_SEPARATOR . 'application' .DIRECTORY_SEPARATOR);