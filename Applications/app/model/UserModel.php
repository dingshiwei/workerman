<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/3 0003
 * Time: 15:01
 */
namespace app\model;
use \service\Mysql;

class UserModel
{
    private static $user_clients = 'user_clients';

    public static function get($vuid = '', $client_id = '', $ip= '')
    {
        if (!$vuid && !$client_id && !$ip) {
            return false;
        }
        if ($vuid) {
            $where['vuid'] = $vuid;
        }
        if ($client_id) {
            $where['client_id'] = $client_id;
        }
        if ($ip) {
            $where['ip'] = $ip;
        }
        if ($db = Mysql::load()) {
            $data = $db->select('*')
                ->from(self::$user_clients)
                ->where($where)
                ->limit(1)
                ->query();
            return $data;
        }
        return false;
    }

}