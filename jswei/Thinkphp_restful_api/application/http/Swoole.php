<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2019/3/3
 * Time: 12:02
 */

namespace app\http;

use app\chat\model\ChatContent;
use app\lib\ChatRedis;
use think\swoole\Server;
use app\lib\ChatTimer;
use think\swoole\facade\Timer;

class Swoole extends Server{

    protected $host = '0.0.0.0';
    protected $port = 9502;
    protected $serverType = 'socket';

    public function __construct(){
        parent::__construct();
    }

    protected $option = [
        'worker_num'=> 4,
        'daemonize'	=> false,
        'backlog'	=> 128,
        'heartbeat_check_interval' => 30,
        'heartbeat_idle_time' => 60,
    ];

    public function onConnect($server,$fd){
//        $fd_info = $server->getClientInfo($fd);
    }

    /**
     * @param $server
     * @param $frame
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function onMessage($server,$frame){
        $send = json_decode($frame->data,true);
        $chat = new ChatRedis();
        $fd = $frame->fd;
        switch ($send['type']){
            case 'init':
                $mine = $chat->bindUser($fd,$send['user_id'])
                    ->bindFd($fd,$send['user_id'])->getUser($fd);
                $friend = $chat->setFriends($fd,$mine['id'])->getFriends($fd);
                $group = $chat->setGroups($fd,$mine['id'])->getGroups($fd);
                $push = self::__json('user_info',[
                    'mine'=>$mine,
                    'friend'=>$friend,
                    'group'=>$group
                ]);
                break;
            case 'message':
                $data = $send['data'];
                $fd =$chat->getFdByUserId($data['to']['id']);
                $massage =$chat->setUserChat($fd,$data);
                $push = self::__json('message',$massage);
                break;
            case 'ticker':
                $t = new ChatTimer($server,$frame->fd,$send);
                Timer::tick(5 * 1000, $t);  //5秒一次
                return;
                break;
            case 'online':
                $chat->setOnline($fd,$send['data']);
                return;
                break;
            case 'sign':
                $chat->setSign($fd,$send['data']);
                return;
                break;
            case 'broadcast':
                $push = self::__json('broadcast',$send['content']);
                foreach($server->connections as $fd) {
                    $server->send($fd, $push);
                }
                echo "broadcast ".count($server->connections)." clients\n";
                /*$start_fd = 0;
                while(true){
                    $conn_list = $server->getClientList($start_fd, 10);
                    if ($conn_list===false or count($conn_list) === 0) {
                        echo "finish\n";
                        break;
                    }
                    $start_fd = end($conn_list);
//                    var_dump($conn_list);
                    foreach($conn_list as $fd) {
                        $server->send($fd, "broadcast");
                    }
                }*/
                return;
                break;
            default:
                $push = 'other';
        }
        $server->push($fd, $push);
    }

    /**
     * @param $server
     * @param $fd
     */
    public function onClose($server,$fd){
        $chat = new ChatRedis();
        $chat->getUserChat($fd,$out)->clear($fd);
        $user_id = $chat->getUserId($fd);
        $chat->setOnline($fd,['user_id'=>$user_id,'online'=>'offline'],false);
        $content = new ChatContent();
        $content->insertAll($out);
        echo "client {$fd} is closed\n";
    }


    /**
     * @param $server
     * @param $taskid
     * @param $from_id
     * @param $data
     */
    public function onTask($server, $taskid, $from_id, $data){
        foreach($this->table as $row){
            $server->send($row['fd'],"{$row['fd']} i am broadcast");
        }
        $server->finish("$data -> OK");
    }

    /**
     * 数据
     * @param string $type
     * @param array $data
     * @return false|string
     */
    protected static function __json($type='message',$data=[]){
        return json_encode([
            'emit'=>$type,
            'data'=>$data
        ]);
    }
}