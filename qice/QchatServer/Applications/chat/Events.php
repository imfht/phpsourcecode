<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);


use \GatewayWorker\Lib\Gateway;

include_once 'helper/RedisHelper.php';
include_once 'EventsHelper.php';


/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    private static $debug = false;

    // 过滤的字词
    private static $msgFilter = [];

    /**
     * 进程启动后初始化数据库连接
     */
    public static function onWorkerStart($worker)
    {
        // 清理在线人
        // 执行重载时会执行这里，，启动时手动清理下
        //self::$_redis->delete(self::RKEY_ONLINE);
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id) {
        // debug

    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param json $message 具体消息
    * $message: to: 指定用户发送，全局发送时 all
    *           type：img, txt, ...
    *           msg: {} 当为img 时为对象格式
    */
   public static function onMessage($client_id, $message) {
       $nowTime = time();
       $msgData = json_decode($message, true);

       if( !$msgData || empty($msgData['type']) ) return;

       if ( in_array($msgData['type'], ['update_user']) && !$_SESSION['manager'] ) {
           return Gateway::sendToCurrentClient(json_encode([
               'type' => $msgData['type'],
               'status' => 0,
               'msg' => '没权限'], JSON_UNESCAPED_UNICODE));
       }

       switch ($msgData['type']) {
           case 'login':
               return self::tLogin($msgData, $client_id);
               break;

           case 'friend_list':
               return self::tFriend($msgData, $client_id);
               break;

           case 'msg_list':
               if ( !empty($msgData['group']) ) {
                   $name = $msgData['from'];
               } else {
                   $name = [$msgData['from'], $msgData['to']];
                   sort($name);
                   $name = implode('-', $name);
               }
               $msg = RedisHelper::getMsg($_SESSION['sn'], $name );

               // 选中的聊天对象
               $_SESSION['selected'] = $msgData['from'];

               Gateway::sendToClient($client_id, json_encode(['type' => 'msg_list', 'data' => $msg]));
               break;

           case 'mark_read':
               RedisHelper::updateUnread($_SESSION['sn'], $msgData, true);
               break;

           case 'user_list':
               $data = RedisHelper::userList($_SESSION['sn']);
               if ( !empty($msgData['online']) ) {
                   foreach ( $data as &$u ) {
                       $u['online'] = Gateway::isUidOnline( self::getUniqueId( $u['name'] ) ) ? true : false;
                   }
               }
               return Gateway::sendToCurrentClient(
                   json_encode(['type' => 'user_list', 'data' => array_values($data)],
                       JSON_UNESCAPED_UNICODE));
               break;

           case 'update_user':
               if ( !preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $msgData['name']) ){
                   return Gateway::sendToCurrentClient(
                       json_encode(['type' => 'update_user', 'status' => 0, 'msg' => '用户名不规范'],
                           JSON_UNESCAPED_UNICODE));
               }

               RedisHelper::updateUser($_SESSION['sn'], $msgData);
               Gateway::sendToCurrentClient(
                   json_encode(['type' => 'update_user', 'status' => 1, 'data' => $msgData, 'msg' => '操作成功'],
                       JSON_UNESCAPED_UNICODE));
               break;

           case 'txt':
           case 'img':
           case 'file':
               $msgData['time'] = $nowTime;
               $msgData['msg'] = $msgData['type'] == 'txt' ? strip_tags($msgData['msg'],'<img>') : $msgData['msg'];
               $message = json_encode($msgData, JSON_UNESCAPED_UNICODE);
               if ( strpos($msgData['to'], '@') === 0 ) {
                   $groupName = substr($msgData['to'], 1);
                   Gateway::sendToGroup(self::getUniqueId( $groupName ), $message);

                   // 存入群消息
                   RedisHelper::saveMsg($_SESSION['sn'], $groupName, $message);

                   // 更新未读
                   $users = RedisHelper::getGroupMember($_SESSION['sn'], $groupName);
                   foreach ( $users as $user) {
                       RedisHelper::updateLasttime($_SESSION['sn'], ['from'=> $user['name'], 'name' => $groupName, 'time' => $msgData['time']]);

                       if ( $user['name'] == $msgData['from'] ) continue;
                       $uniqueId = self::getUniqueId( $user['name'] );
                       if ( Gateway::isUidOnline($uniqueId) ) {
                           $session = Gateway::getSession(Gateway::getClientIdByUid($uniqueId)[0]);
                           if ( isset($session['selected']) && $session['selected'] == $groupName ) {
                               continue;
                           }
                       }

                       RedisHelper::updateUnread($_SESSION['sn'], ['to' => $user['name'], 'from' => $groupName, 'time' => $nowTime]);
                   }
               } else {
                   $uniqueId = self::getUniqueId( $msgData['to'] );
                   //Gateway::sendToUid([self::getUniqueId( $msgData['from'] ), $uniqueId], $message);

                   if ( !Gateway::isUidOnline($uniqueId) ) {
                       // 更新未读
                       $rs = RedisHelper::updateUnread($_SESSION['sn'], $msgData);
                   } else {
                       /// 收件人选择的聊天对象
                       $session = Gateway::getSession(Gateway::getClientIdByUid($uniqueId)[0]);
                       if ( !isset($session['selected']) || $session['selected'] != $msgData['to'] ) {
                           // 更新未读
                           $rs = RedisHelper::updateUnread($_SESSION['sn'], $msgData);
                       }
                   }

                   $msgData['new_chat'] = $rs;
                   Gateway::sendToUid([self::getUniqueId( $msgData['from'] ), $uniqueId], json_encode($msgData, JSON_UNESCAPED_UNICODE));

                   $name = [$msgData['from'], $msgData['to']];
                   sort( $name );
                   $name = implode('-', $name);
                   RedisHelper::saveMsg($_SESSION['sn'], $name, $message);
                   RedisHelper::updateLasttime($_SESSION['sn'], ['from'=> $msgData['from'], 'name' => $msgData['to'], 'time' => $msgData['time']]);
                }
                break;

            default:
                if( !method_exists('EventsHelper', $msgData['type']) ) return;
                call_user_func_array(['EventsHelper', $msgData['type']], [$msgData]);
                break;
       }
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {

   }

   public static function getUniqueId($uid) {
       return $_SESSION['sn'] .'-'. $uid;
   }

   private static function tFriend($data, $cid){
       $friends = RedisHelper::getFriend($_SESSION['sn'], $_SESSION['name']);

       $_time = [];
       $_unread = [];
       foreach ( $friends as $friend ) {
           if ( $friend['group'] ) {
               Gateway::joinGroup($cid, self::getUniqueId( $friend['name'] ));
           }

           $_time[] = isset($friend['last_time']) ? $friend['last_time'] : time();
           $_unread[] = isset($friend['unread']) ? $friend['unread'] : 0;
       }

       // 排序
       array_multisort($_time, SORT_DESC, $friends);

       return Gateway::sendToClient($cid, json_encode(['type' => 'friend_list', 'data' => array_values($friends)]));
   }

   private static function tLogin($data, $cid){
       $user = RedisHelper::getUser($_SESSION['sn'], $data);
       if ( !isset($user['passwd']) ) {
           if ( $data['name'] == 'admin' ) {
               $user = [
                   'id'     => 1,
                   'name' => 'admin',
                   'passwd' => '111111',
                   'img' => ''
               ];

               RedisHelper::getUser($_SESSION['sn'], $user, true);
           } else {
               return Gateway::sendToClient($cid, json_encode(['type' => 'login', 'status' => 0, 'msg' => '用户不存在']));
           }
       }

       if ( isset($data['passwd']) && $user['passwd'] != $data['passwd'] ) {
           return Gateway::sendToClient($cid, json_encode(['type' => 'login', 'status' => 0, 'msg' => '密码错误']));
       }

       if ( Gateway::isUidOnline(self::getUniqueId( $user['name']) ) ) {
           Gateway::sendToUid(self::getUniqueId( $user['name']), json_encode([
               'type' => 'warn', 'code' => 1, 'msg' => '此帐号在其他地方登录'
           ], JSON_UNESCAPED_UNICODE));
       }

       $_SESSION['name'] = $user['name'];
       $_SESSION['manager'] = ($user['name'] == 'admin' || !empty($user['manager'])) ? true : false;

       Gateway::bindUid($cid, self::getUniqueId( $user['name'] ));

       if ( isset($data['passwd']) ) {
           $user['manager'] = $_SESSION['manager'];
           $msg = ['type' => 'login', 'status' => 1, 'msg' => '登录成功', 'data' => $user];
           return Gateway::sendToClient($cid, json_encode($msg));
       }
   }

    public static function record($json){
        // 用户每分钟发送的消息记录
        $msg = json_decode($json, true);
        if ( $_SESSION['client_uid'] && !in_array($msg['type'], ['lottery', 'standings']) ) {
            $nu = RedisHelper::getSendNum($_SESSION['sn'], $_SESSION['client_uid']);
            $min = date('H:i');
            $n = 1;
            if ($nu[0] == $min) $n += $nu[1];
            RedisHelper::getSendNum($_SESSION['sn'], $_SESSION['client_uid'], [$min, $n]);
        }

        RedisHelper::record($_SESSION['sn'], $json);
    }


    public static function log( $log ){
        if ( !self::$debug ) return;
        $dateTime = date('Y-m-d H:i:s');
        $log = $dateTime .' '. $log;

        file_put_contents('log/'. substr($dateTime, 0, 10) .'.txt', $log, FILE_APPEND);
        Gateway::sendToGroup('debug', $log);
    }
}