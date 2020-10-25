<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace  nb\server\assist;

use nb\Config;
use nb\Console;
use nb\server\Driver;
/**
 * Swoole
 *
 * @package nb\server
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
abstract class Swoole extends Driver {

    protected $swoole;

    protected $options = [];

    protected $callback;

    public function __construct($options=[]) {
        $this->options = array_merge($this->options,$options);
        $register = get_class_methods($this->options['register']);
        $register and $this->call = array_intersect($this->call,$register);
    }

    abstract function run();

    public function start($daemonize=true) {
        echo Console::driver()->logo();
        $this->evenCheck();
        $conf = Config::$o->server;
        if ($pid = $this->getpid()) {
            echo sprintf("other swoole {$conf['driver']} server run at pid %d\n", $pid);
            exit(1);
        }
        echo 'server pattern       '.$conf['driver']."\n";
        echo 'listen address       '.$conf['host']."\n";
        echo 'listen port          '.$conf['port']."\n";
        echo 'worker num           '.$conf['worker_num']."\n";
        echo 'task worker num      '.$conf['task_worker_num']."\n";
        echo 'swoole version       '.phpversion('swoole')."\n\n";
        $this->run($daemonize);
    }

    public function restart() {
        if (!$pid = $this->getpid()) {
            echo "Swoole HttpServer not run\n";
            exit(1);
        }
        posix_kill($pid, SIGTERM);
        echo "swoole httpserver stop\n";
        echo "swoole httpserver start.";
        for($i=1;$i<3;$i++){
            sleep($i);
            echo('.');
        }
        echo "\nrestart +ok\n";
        $this->run();
    }

    public function stop() {
        if (!$pid = $this->getpid()) {
            echo "Swoole {$this->options['driver']} server not run\n";
            exit(1);
        }
        posix_kill($pid, SIGTERM);
        echo "swoole {$this->options['driver']} server stoped\n";
    }

    public function status() {
        if ($pid = $this->getpid()) {
            echo sprintf("swoole {$this->options['driver']} server run at pid %d \n", $pid);
        }
        else {
            echo "swoole {$this->options['driver']} server not run\n";
        }
    }

    public function reload() {
        if (!$pid = $this->getpid()) {
            echo "swoole {$this->options['driver']} server not run\n";
            exit(1);
        }
        posix_kill($pid, SIGUSR1);
        echo "swoole {$this->options['driver']} server reloaded\n";
    }

    public function getpid() {
        $enable_pid = $this->options['enable_pid'];
        $pid = file_exists($enable_pid) ? file_get_contents($enable_pid) : 0;
        // 检查进程是否真正存在
        if ($pid && !posix_kill($pid, 0)) {
            posix_get_last_error() === 3 and $pid = 0;
        }
        return $pid;
    }

    public function evenCheck(){
        if(phpversion() < 5.6){
            $version =  phpversion();
            die("php version must >= 5.6,the current version is {$version}\n");
        }
        if(phpversion('swoole') < 1.8){
            $version =  phpversion('swoole');
            die("swoole version must >= 1.9.5,the current version is {$version}\n");
        }

    }

    public function opCacheClear(){
        if(function_exists('apc_clear_cache')){
            apc_clear_cache();
        }
        if(function_exists('opcache_reset')){
            opcache_reset();
        }
    }

    protected function error($e) {
        //因为需要模拟die函数,所以此处需要catch处理
        if($e->getMessage() === 'die') {
            return;
        }
        throw new \ErrorException(
            $e->getMessage(),
            $e->getCode(),
            1,
            $e->getFile(),
            $e->getLine(),
            $e->getPrevious()
        );
    }

    public function __call($name, $arguments) {
        // TODO: Implement __call() method.
        return call_user_func_array([$this->swoole,$name],$arguments);
    }

    public function __get($name) {
        // TODO: Implement __get() method.
        return $this->swoole->$name;
    }

    /**
     * Server启动在主进程的主线程回调此函数
     * 在此事件之前Swoole Server已进行了如下操作
     *   已创建了manager进程
     *   已创建了worker子进程
     *   已监听所有TCP/UDP端口
     *   已监听了定时器
     * onStart回调中，仅允许echo、打印Log、修改进程名称。不得执行其他操作。
     * onWorkerStart和onStart回调是在不同进程中并行执行的，不存在先后顺序。
     * @param \swoole\server $server
     */
    public function __start(\swoole\Server $server) {
        $pid = posix_getpid();
        file_put_contents($this->options['enable_pid'], $pid);

        if(method_exists($this->callback,'start')) {
            $this->callback->start($server);
        }
    }

    /**
     * 当服务关闭时触发
     * 在此之前Swoole Server已进行了如下操作
     *   已关闭所有线程
     *   已关闭所有worker进程
     *   已close所有TCP/UDP监听端口
     *   已关闭主Rector
     * @param swoole_server $server
     */
    public function __shutdown(\swoole\Server $server) {
        $path = $this->options['enable_pid'];
        file_exists($path) and unlink($path);

        if(method_exists($this->callback,'shutdown')) {
            $this->callback->shutdown($server);
        }
    }

}