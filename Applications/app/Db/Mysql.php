<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/26 0026
 * Time: 10:30
 */

namespace app\db;
use \Workerman\MySQL\Connection;

class Mysql
{
    private static $db;

    public static function load($env= 'production')
    {
        $db_config = require "./../../../phinx.php";
        if (empty($db_config[$env])) return false;
        $db = $db_config[$env];
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