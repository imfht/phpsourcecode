<?php
namespace app\common;

use think\worker\Http as WorkerHttpServer;
use think\facade\Log;
use app\common\Worker;

class HttpServer extends WorkerHttpServer
{
    public $uniqid = null;


    /**
     * 架构函数
     * @access public
     * @param  string $host 监听地址
     * @param  int    $port 监听端口
     * @param  array  $context 参数
     */
    public function __construct($host, $port, $context = [])
    {
        $this->worker = new Worker('http://' . $host . ':' . $port, $context);

        $this->name = 'WebAdmin';

        // 设置回调
        foreach ($this->event as $event) {
            if (method_exists($this, $event)) {
                $this->worker->$event = [$this, $event];
            }
        }
    }


    public function onWorkerStart($worker)
    {
        Log::debug('WebAdmin worker start,listen to '.$worker->getSocketName());
        parent::onWorkerStart($worker);   
    }
}
