<?php
/**
 * 系统核心类，负责进程调度
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/26
 * Time: 15:22
 */

namespace Bjask;

use Bjask\Queue\Queue;


class Kernel
{
    private $version = '1.0';
    private $config = [];
    private $logger = null;
    private $queue = null;
    private $process = null;
    private static $cmds = ['start', 'stop', 'restart', 'status', 'help'];

    public function __construct()
    {
        $this->config = Config::load()->get();
        $this->logger = Logger::getInstance($this->config['log']);
        $this->queue = Queue::getQueue($this->config['queue'], $this->logger);
        $task = new Task($this->queue, $this->logger);
        $this->process = new Process($this->config, $this->logger, $task);//todo:非启动进入需优化
    }

    public function run()
    {
        global $argv, $argc;
        $cmd = $argv[$argc - 1];
        !in_array($cmd, self::$cmds) && $cmd = 'help';
        return $this->{'on' . ucfirst($cmd)}();
    }

    public function onStart()
    {
        $this->process->start();
    }

    public function onStop()
    {
        $this->process->sendSigno(SIGTERM);//SIGKILL信号无法被捕获
    }

    public function onRestart()
    {
        $this->logger->log('master process restarting...');
        $this->process->sendSigno(SIGTERM);//发送结束主进程信号
        sleep(3);
        $this->onStart();
    }

    public function onStatus()
    {
        //发送用户自定义信号
        if ($this->process->sendSigno()) {
            /*  swoole_async_readfile('./logs/pids/master.status', function($filename, $content) {
                  echo $content;
              });*/
            sleep(1);
            echo file_get_contents($this->process->pidFilePath . $this->process->masterStatusFile);
            exit;
        }
        die('获取进程状态信息失败' . PHP_EOL);
    }

    /**
     * 帮助提示
     */
    public function onHelp()
    {
        echo <<<EOF
Bjask version:{$this->version}
Usage: php server.php [options]

Options:
    start   [启动]
    stop    [停止]
    restart [重启]
    status  [查看]
    help    [帮助]
    
    \n
EOF;
    }
}