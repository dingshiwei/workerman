<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21 0021
 * Time: 16:45
 */
namespace app\events_handler;
use app\db\Mysql;

class User {

    private static $user_clients = 'user_clients';

    /**
     * @param $client_id
     * @param $message
     */
    public static function login($client_id, $message)
    {
        if ($client_id) {
            if ($db = Mysql::load()) {
                // 查询是否已连接
                $client_log = $db->select('*')
                    ->from(self::$user_clients)
                    ->where("client_id='{$client_id}'")
                    ->limit(1)
                    ->query();
                if ($client_log && !empty($client_log)) {
                    return true;
                }
                //如果client_id不同 则删除旧的，新增(或者删除都通过心跳机制 检测不到则删除)
                list($vuid, $ip) = $message;
                $log_id = $db->insert(self::$user_clients)
                    ->cols(array(
                        'vuid' => $vuid,
                        'ip' => $ip,
                        'update' => date("Y-m-d H:i:s"),
                        'create_at' => date("Y-m-d H:i:s")
                    ))
                    ->query();
                return $log_id;
            }
        }
        return 0;
    }

    /**
     * @param $client_id
     * @param $message
     */
    public static function preBind($client_id, $message)
    {

    }

    private function deleteClient($vuid, $client)
    {
        if (!$vuid && !$client) {
            return false;
        }
        if ($vuid) {
            $where['vuid'] = $vuid;
        }
        if ($client) {
            $where['vuid'] = $vuid;
        }
        if ($db = Mysql::load()) {
            $db->delete(self::$user_clients)
                ->where($where)
                ->limit(100)
                ->query();
            return true;
        }
        return false;
    }
}
