<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\server;

use nb\Config;
use nb\Debug;
use nb\Dispatcher;
use nb\Pool;
use nb\server\assist\Swoole;

/**
 * Tcp
 *
 * @package nb\server
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/1
 *
 * @property  \swoole\Server swoole
 */
class Tcp extends Swoole {

    public $fd;

    protected $options = [
        'driver'=>'tcp',
        'register'=>'\\nb\\event\\Swoole',//注册一个类，来实现swoole自定义事件
        'host'=>'0.0.0.0',
        'port'=>9502,
        'mode' => SWOOLE_PROCESS,
        'sock_type' => SWOOLE_SOCK_TCP,
        'max_request'=>'',//worker进程的最大任务数
        'worker_num'=>'',//设置启动的worker进程数。
        'dispatch_mode'=>2,//据包分发策略,默认为2
        'debug_mode'=>3,
        'enable_gzip'=>0,//是否启用压缩，0为不启用，1-9为压缩等级
        'enable_log'=>__APP__.'tmp'.DS.'swoole-tcp.log',
        'enable_pid'=>'/tmp/swoole.pid',
        'daemonize'=>true
    ];

    protected $call = [
        //'start',
        //'shutdown',
        'workerStart',
        'workerStop',
        'workerExit',
        'timer',
        'connect',
        'receive',
        'packet',
        'close',
        'bufferFull',
        'bufferEmpty',
        'task',
        'finish',
        'pipeMessage',
        'workerError',
        'managerStart',
        'managerStop'
    ];

    public function run() {
        $opt = $this->options;
        //设置server参数
        $server = new \swoole\Server($opt['host'], $opt['port'],$opt['mode'],$opt['sock_type']);
        $server->set($this->options);

        //注册server启动和结束回调
        $server->on('start',[$this,'__start']);
        $server->on('shutdown',[$this,'__shutdown']);

        //设置server请求数据处理回调事件
        $server->on('receive',[$this,'receive']);

        $callback = new $this->options['register']();
        foreach ($this->call as  $v) {
            $server->on($v,[$callback,$v]);
        }
        $this->swoole = $server;
        //启动server
        $server->start();
    }

    public function receive($server, $fd, $reactor_id, $data) {
        try {
            ob_start();
            Config::$o->sapi='tcp';
            Pool::destroy();
            $this->fd = $fd;
            \nb\Request::driver($fd,$reactor_id,$data);
            Dispatcher::run($data);
        }
        catch (\Throwable $e) {
            $this->error($e);
        }
        Debug::end();
        $data = ob_get_contents() and $this->reply($data);
        ob_end_clean();
    }

    /**
     * 向客户端发送数据 https://wiki.swoole.com/wiki/page/p-server/send.html
     *
     *  * $data，发送的数据。TCP协议最大不得超过2M，UDP协议不得超过64K
     *  * 发送成功会返回true，如果连接已被关闭或发送失败会返回false
     *
     * TCP服务器
     *  * send操作具有原子性，多个进程同时调用send向同一个连接发送数据，不会发生数据混杂
     *  * 如果要发送超过2M的数据，可以将数据写入临时文件，然后通过sendfile接口进行发送
     *
     * UDP服务器
     *  * send操作会直接在worker进程内发送数据包，不会再经过主进程转发
     *  * 在外网服务中发送超过64K的数据会分成多个传输单元进行发送，如果其中一个单元丢包，会导致整个包被丢弃。所以外网服务，建议发送1.5K以下的数据包
     *
     * @param int $fd
     * @param string $data
     * @return bool
     */
    public function send($fd, $data){
        if($this->swoole->exist($fd)) {
            return $this->swoole->send($fd,$data);
        }
        return false;
    }

    /**
     * 向当前连接客户端发送数据
     * @param int $fd
     * @param string $data
     * @return bool
     */
    public function reply($data){
        return $this->send($this->fd,$data);
    }

    /**
     * 关闭客户端连接
     * 不要在close之后写清理逻辑。应当放置到onClose回调中处理
     *
     * @param int $fd
     * @param bool $reset 设置为true会强制关闭连接，丢弃发送队列中的数据
     * @return bool
     */
    public function close($fd=0, $reset = false) {
        $fd or $fd = $this->fd;
        return $this->swoole->close($fd,$reset);
    }

}