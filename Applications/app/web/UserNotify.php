<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/3 0003
 * Time: 14:37
 */
namespace app\web;
use app\model\UserModel;
use \GatewayWorker\Lib\Gateway;


class UserNotify extends MyController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function bindSuccess()
    {
        $vuid = (int)$_POST['vuid'];
        if (!$vuid) {
            return $this->apiReturn(array('state'=>3001, 'mess'=>'缺少必要字段vuid'));
        }
        // 找出当前账户对应的客户端id
        if ($data  = UserModel::get($vuid)) {
            $client_id = $data['client_id'];
        } else {
            return $this->apiReturn(array('state'=>3002, 'mess'=>'没有对应的在线客户端'));
        }
        // 判断是否在线
        if (Gateway::isOnline($client_id)) {
            return $this->apiReturn(array('state'=>3003, 'mess'=>'当前客户端离线'));
        }
        // 发出绑定成功通知
        Gateway::sendToClient($client_id, json_encode(array('type'=>'user_bind', 'data'=>['vuid'=>$vuid])));
    }

}