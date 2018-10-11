<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/3 0003
 * Time: 14:11
 */
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21 0021
 * Time: 14:46
 */
if (!function_exists('env')) {
    function env($env, $default) {
        return $env ? : $default;
    }
}

$config = array(
    'name' => 'WebServer',
    'webserver_address' => env($_ENV['WEBSERVER_ADDRESS'], '0.0.0.0:80'),
    'webserver_name' => env($_ENV['WEBSERVER_NAME'], 'webserver.shop.ci123.com'),
    'count' => env($_ENV['WEBSERVER_COUNT'], 1)
);