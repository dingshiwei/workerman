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
    'register_address' => env($_ENV['REGISTER_ADDRESS'],'0.0.0.0:2020')
);