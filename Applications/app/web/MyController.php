<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/3 0003
 * Time: 15:40
 */

namespace app\web;
use \GatewayWorker\Lib\Gateway;

class MyController
{
    public function __construct()
    {
        require __DIR__.'/../config/gateway.php';
        Gateway::$registerAddress = $config['register_address'];
        // Gateway::$secretKey = Config::getConfig('SecretKey');
    }

    public function apiReturn($arr)
    {
        echo json_encode($arr);
        return ;
    }
}