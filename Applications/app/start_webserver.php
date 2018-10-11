<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/3 0003
 * Time: 14:03
 */
use \Workerman\Worker;
use \app\web\core\WebServer;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/config/webserver.php';

// WebServer
$web = new WebServer("http://{$config['webserver_address']}");

// 4 processes
$web->count = $config['count'];

// Set the root of domains
$web->addRoot($config['webserver_name'], '\app\web\\');
// run all workers
Worker::runAll();
