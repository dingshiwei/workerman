<?php
if (!function_exists('env')) {
    function env($env, $default) {
        return $env ? : $default;
    }
}

$config = array(
    'name' => 'PrintsWorker',
    'count' => env($_ENV['WORKER_COUNT'], 4),
    'register_address' => env($_ENV['REGISTER_ADDRESS'],'0.0.0.0:2020')
);
