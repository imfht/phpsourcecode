<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace  nb\server;

use nb\Config;
use nb\Debug;
use nb\Dispatcher;
use nb\Pool;
use nb\server\assist\Swoole;

/**
 * Http
 *
 * @package nb\server
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
class Http extends Swoole {

    //必须的默认的配置
    protected $options = [
        'driver'=>'tcp',
        'register'=>'nb\\event\\Swoole',//注册一个类，来实现swoole自定义事件
        'host'=>'0.0.0.0',
        'port'=>9501,
        'mode' => SWOOLE_PROCESS,
        'sock_type' => SWOOLE_SOCK_TCP,
        'max_request'=>'',//worker进程的最大任务数
        'worker_num'=>'',//设置启动的worker进程数。
        'dispatch_mode'=>2,//据包分发策略,默认为2
        'debug_mode'=>3,
        'enable_gzip'=>0,//是否启用压缩，0为不启用，1-9为压缩等级
        'enable_log'=>'tmp'.DS.'swoole-http.log',
        'enable_pid'=>'/tmp/swoole.pid',
        'daemonize'=>true
    ];

    //支持的回调函数
    protected $call = [
        //'start',
        //'shutdown',
        'workerStart',
        'workerStop',
        'workerExit',
        'timer',
        'packet',
        'request',
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

    /**
     * 启动Swoole服务
     */
    public function run() {
        $opt = $this->options;

        //设置server参数
        $server =  new \swoole\http\Server($opt['host'], $opt['port'],$opt['mode'],$opt['sock_type']);
        $server->set($this->options);

        //注册server启动和结束回调
        $server->on('start', [$this,'__start']);
        $server->on('shutdown', [$this,'__shutdown']);

        //设置httpserver数据请求回调事件
        $server->on('request', [$this,'request']);

        $callback = new $this->options['register']();
        foreach ($this->call as  $v) {
            $server->on($v,[$callback,$v]);
        }
        $this->swoole = $server;
        $server->start();
    }

    public function request(\swoole\http\Request $request, \swoole\http\Response $response) {
        try {
            ob_start();
            Config::$o->sapi='http';
            Pool::destroy();
            Pool::set('\swoole\http\Request', $request);
            Pool::set('\swoole\http\Response',$response);
            Dispatcher::run();
        }
        catch (\Throwable $e) {
            $this->error($e);
        }
        Debug::end();
        $response->end(ob_get_contents());
        ob_end_clean();
    }

}