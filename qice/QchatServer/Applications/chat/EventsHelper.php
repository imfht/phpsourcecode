<?php

use \GatewayWorker\Lib\Gateway;

class EventsHelper {

    /*
     * 直接在处理未读消息时双向加入好友
    public static function add_friend ( $msg ) {
        RedisHelper::addFriend($_SESSION['sn'], $msg['from'], $msg['to']);

        if ( Gateway::isUidOnline( Events::getUniqueId( $msg['to'] ) )) {
            Gateway::sendToUid(Events::getUniqueId( $msg['to'] ),
                json_encode([
                    'type' => 'add_friend', 
                    'from' => $msg['from'], 
                    'to' => $msg['to']], JSON_UNESCAPED_UNICODE));
        }
        //Gateway::sendToCurrentClient(json_encode(['type' => 'add_friend'], JSON_UNESCAPED_UNICODE));
    }
    */

    public static function join_group ( $msg ) {
        $users = RedisHelper::joinGroup($_SESSION['sn'], $msg['group_name'], $msg['users'] );
        foreach ( $users as $user ) {
            $_cid = Gateway::getClientIdByUid(Events::getUniqueId($user['name']));
            if ( !empty($_cid[0]) ) Gateway::joinGroup($_cid[0], Events::getUniqueId( $msg['group_name'] ));
        }

        $data = [
            'type' => 'join_group',
            'status' => 1,
            'users'  => $users,
            'create' => $msg['create'],
            'group_name' => $msg['group_name'],
            'from'  => $msg['from']
        ];
        Gateway::sendToGroup(Events::getUniqueId( $msg['group_name']), json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public static function passwd($msg) {
        $user = RedisHelper::getUser($_SESSION['sn'], $msg);
        if ( !empty( $user['name'] ) && $msg['passwd'] != $user['passwd'] ) {
            return Gateway::sendToCurrentClient(json_encode([
                'type' => 'passwd',
                'status' => 0,
                'msg' => '修改失败'
            ], JSON_UNESCAPED_UNICODE));
        }

        $data = [
            'name'  => $msg['name'],
            'passwd' => $msg['passwd2'],
            'img'    => '',
        ];
        RedisHelper::getUser($_SESSION['sn'], $data, true);

        Gateway::sendToCurrentClient(json_encode([
            'type' => 'passwd',
            'status' => 1,
            'msg' => '修改成功'
        ], JSON_UNESCAPED_UNICODE));
    }

    public static function leave_group($msg) {
        $group = RedisHelper::leaveGroup($_SESSION['sn'], $msg);

        if ( !empty($group) ) {
            $cid = Gateway::getClientIdByUid(Events::getUniqueId($_SESSION['name']));
            Gateway::leaveGroup($cid[0], Events::getUniqueId( $msg['group_name']));
            $data = [
                'type' => 'join_group',
                'status' => 1,
                'users'  => $group,
                'create' => 0,
                'group_name' => $msg['group_name'],
                'from'  => $msg['from']
            ];
            Gateway::sendToGroup(Events::getUniqueId( $msg['group_name']), json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

    public static function del_friend($msg) {
        RedisHelper::delFriend($_SESSION['sn'], $msg);
    }
}