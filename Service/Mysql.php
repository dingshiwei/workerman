<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/26 0026
 * Time: 10:30
 */

namespace service;
use \Workerman\MySQL\Connection;

class Mysql
{
    private static $db;

    public static function load($env = 'production')
    {
        try {
            $db_config = require_once(__DIR__.'/../phinx.php');
        } catch (\Exception $e) {
            // can not load the DB config
        }
        if (empty($db_config['environments'][$env])) return false;
        $db = $db_config['environments'][$env];
        if (!self::$db) {
            try {
                self::$db = new Connection($db['host'],$db['port'], $db['user'], $db['pass'], $db['name']);
            } catch (\Exception $e) {
                //to do log the error
            }
        }
        return self::$db;
    }
}