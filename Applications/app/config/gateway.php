<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21 0021
 * Time: 14:46
 */
$config = array(
    'name' => 'PrintsGateWay',
    'count' => $_ENV['GATEWAY_COUNT'] ? :4,
    'address' => $_ENV['GATEWAY_ADDRESS'] ? : '0.0.0.0:2030',
    'lan_ip' => $_ENV['GATEWAY_LANIP'] ? : '127.0.0.1',
    'start_port' => $_ENV['GATEWAY_START_PORT'] ? : 2900,
    'register_address' => $_ENV['REGISTER_ADDRESS'] ? : '0.0.0.0:2020'
);
