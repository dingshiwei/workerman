<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21 0021
 * Time: 14:46
 */
function env($env, $default) {
    return $env ? : $default;
}

$config = array(
    'name' => 'PrintsGateWay',
    'count' => env($_ENV['GATEWAY_COUNT'], 4),
    'address' => env($_ENV['GATEWAY_ADDRESS'],'0.0.0.0:2030'),
    'lan_ip' => env($_ENV['GATEWAY_LANIP'], '127.0.0.1'),
    'start_port' => env($_ENV['GATEWAY_START_PORT'], 2900),
    'register_address' => env($_ENV['REGISTER_ADDRESS'],'0.0.0.0:2020')
);
