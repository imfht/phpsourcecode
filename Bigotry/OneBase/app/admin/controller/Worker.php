<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;

use think\worker\Server;

/**
 * Worker控制器
 */
class Worker extends Server
{

    protected $socket = 'websocket://0.0.0.0:2346';
    
    /**
     * 启动
     */
    public function index()
    {
        
        parent::start();
    }
    
    /**
     * 收到客户端信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {

        $data_arr = json_decode($data, true);

        $data_arr['msg']  = strip_tags(htmlentities(stripslashes($data_arr['msg'])));
        
        $data_arr['time'] = date("Y-m-d H:i:s");

        if ($data_arr['msg'] !== 'ping') {
            
            $data_json = json_encode($data_arr);
            
            foreach ($this->worker->connections as $connection) {

                $connection->send($data_json);
            }

            $file_path = '../runtime/log/chat/'.date("Ym").'.txt';
            
            !file_exists($file_path) && fopen($file_path,'w');
            
            file_put_contents($file_path, $data_json . PHP_EOL, FILE_APPEND);
            
        } else {
            
            $data_arr['msg'] = 'ping';
            
            $data_json = json_encode($data_arr);
            
            $connection->send($data_json);
        }
    }
    
    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {


    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        

    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {


    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {

    }
}

