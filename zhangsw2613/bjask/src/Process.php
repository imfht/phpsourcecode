<?php
/**
 * 进程类，负责主、子进程任务处理
 * 进程重命名
 * 单个任务最大子进程数
 * 单个任务最大延时时间
 * 子进程最长等待时间
 * 队列积压最大长度报警
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/29
 * Time: 14:29
 */

namespace Bjask;

use Bjask\Message\Message;

class Process
{
    const STATUS_RUNNING = 'running';//主进程运行状态
    const STATUS_WAITING = 'waiting';
    const STATUS_STOP = 'stop';

    public $processName = '';//统一进程名前缀
    public $isDaemon = 1;//是否守护进程运行
    public $pidFilePath = '/';//pid保存目录
    public $maxChildProcess = 100;//最大子进程总数
    public $maxQueue = 100;//每个任务最大队列长度
    public $queueTickTime = 1000 * 10;//定时检查队列长度的时间间隔
    public $maxExecuteTime = 10;//子进程最大执行时间s
    public $openMessage = 1;//开启消息提醒
    public $runSleep = 100;//任务执行完暂停毫秒数
    public $messageType = 'ding';//报警消息类型
    public $startRunTime = '';//开始运行时间
    public $runningStatus = '';//当前主进程运行状态
    public $masterProcessName = '';//主进程名
    public $masterPid = 0;//主进程pid
    public $masterPidFile = 'master.pid';//主进程pid文件名
    public $masterStatusFile = 'master.status';//主进程状态记录文件
    public $topicPids = [];//方便统计每个topic的子进程数
    public $pids = [];//子进程pid
    public $topics = [];
    public $logger = null;
    public $task = null;
    public $message = null;

    public function __construct(array $config, Logger $logger, Task $task)
    {
        $this->processName = $config['processName'];
        $this->isDaemon = $config['isDaemon'];
        $this->pidFilePath = $config['pidFilePath'];
        $this->maxChildProcess = $config['maxChildProcess'];
        $this->maxQueue = $config['maxQueue'];
        $this->queueTickTime = $config['queueTickTime'];
        $this->maxExecuteTime = $config['maxExecuteTime'];
        $this->openMessage = $config['openMessage'];
        $this->runSleep = $config['runSleep'];
        $this->messageType = $config['messageType'];
        $this->topics = $config['topic'];
        $this->logger = $logger;
        $this->task = $task;
        $this->message = Message::init($this->logger, $this->messageType, $config['message']);
        foreach (array_keys($this->topics) as $name) {
            $this->topicPids[$name] = 0;//初始化计数
        }
        if (!is_writable($this->pidFilePath)) {
            die("pid文件目录不可写，请修改后再试!" . PHP_EOL);
        }
    }

    /**
     * 开启进程活动
     */
    public function start()
    {
        try {
            $this->logger->log('master process starting...');
            $this->startMaster();
            $this->registSignal();//主进程不要阻塞，否则有可能无法收到信号
            $this->logger->log('master process started');
        } catch (\Exception $e) {
            $this->logger->log('master process start error on line: ' . $e->getLine() . ' msg:' . $e->getMessage(), $this->logger::LEVEL_ERROR);
        } catch (\Throwable $e) {
            $this->logger->log('master process start error on line: ' . $e->getLine() . ' msg:' . $e->getMessage(), $this->logger::LEVEL_ERROR);
        }

    }

    /**
     * 启动主进程
     */
    private function startMaster()
    {
        if ($this->processIsRunning($this->pidFilePath . $this->masterPidFile)) {
            die("主进程已开启，请先关闭后再试!" . PHP_EOL);
        }
        if ($this->isDaemon) {
            //1.9.1或更高版本修改了默认值，现在默认nochir和noclose均为true
            //蜕变为守护进程时，该进程的PID将重新fork，可以使用getmypid()来获取当前的PID
            \swoole_process::daemon();
        }
        $this->masterPid = getmypid();
        $info['pid'] = $this->masterPid;
        $this->putPidInfo($info, $this->pidFilePath . $this->masterPidFile);
        $this->masterProcessName = $this->processName . ' ' . 'master';
        $this->setProcessName($this->masterProcessName);
        $this->runningStatus = self::STATUS_RUNNING;
        $this->startRunTime = microtime(true);
        if ($this->queueTickTime && $this->openMessage) {
            $this->registQueueTick();
        }
        $this->createProcess();
    }

    /**
     * 关闭进程活动
     */
    private function stopAllProcessAndExit()
    {
        $this->logger->log('stopping master process...');
        $this->runningStatus = self::STATUS_STOP;
        //先关闭所有子进程
        if (count($this->pids)) {
            foreach ($this->pids as $info) {
                \swoole_process::kill($info['pid']);//触发SIGCHLD信号
            }
            //这边如果直接exit主进程，信号处理程序将不执行
        } else {
            $this->exitMaster();
        }
    }

    /**
     * 结束主进程
     */
    private function exitMaster()
    {
        sleep(1);
        @unlink($this->pidFilePath . $this->masterPidFile);
        @unlink($this->pidFilePath . $this->masterStatusFile);
        $this->logger->log('master process stopped...');
        exit();//信号触发执行
    }

    /**
     * 根据任务名创建子进程
     * @param string $topic_name
     * @param int $num
     * @return bool
     */
    public function createProcess($topic_name = '', $num = 0)
    {
        if (count($this->pids) >= $this->maxChildProcess) {
            $this->logger->log("子进程总数超过上限，等待子进程销毁", $this->logger::LEVEL_NOTICE);
            return false;
        }
        $topics = empty($topic_name) ? $this->topics : [$topic_name => $this->topics[$topic_name]];
        foreach ($topics as $name => $topic) {
            $num = $num > 0 ? $num : $topic['minProcess'];
            $pids = [];
            for ($i = 0; $i < $num; $i++) {
                if (isset($this->topicPids[$name]) && $this->topicPids[$name] >= $topic['maxProcess']) {
                    break;
                }
                $process = new \swoole_process(function (\swoole_process $process) {
                    try {
                        $begin_time = microtime(true);
                        $this->setProcessName($process->name);
                        while (($begin_time + $this->maxExecuteTime) > time()) {
                            $this->task->openConnect($process->topic_name);
                            if($this->task->len() > 0){
                                $this->task->run();
                            }
                            $this->task->closeConnect();
                            usleep($this->runSleep);
                        }
                    } catch (\Exception $e) {
                        $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
                    } catch (\Throwable $e) {
                        $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
                    }
                });
                $process->name = $this->processName . ' ' . "child $name";//自定义进程名
                $process->topic_name = $name;
                $pid = $process->start();
                $pids[$pid] = ['pid' => $pid, 'task_name' => $name];
                $this->topicPids[$name]++;
                $this->runningStatus == self::STATUS_WAITING && $this->runningStatus = self::STATUS_RUNNING;//如果当前为waiting状态修改主进程为running状态
            }
            $this->pids += $pids;
            $this->logger->log("任务名:{$name}，增加[" . count($pids) . "]个子进程");
        }
        return true;
    }

    /**
     * 发送信号
     * @param int $signo
     * @return bool
     */
    public function sendSigno($signo = SIGUSR1)
    {
        $master_info = $this->getPidInfo($this->pidFilePath . $this->masterPidFile);
        if (!isset($master_info['pid']) || !\swoole_process::kill($master_info['pid'], 0)) {
            die('主进程未启动，请先启动主进程再试!' . PHP_EOL);
        }
        $this->logger->log('preparing to send signal {' . $signo . '} to master process');
        if (\swoole_process::kill($master_info['pid'], $signo)) {
            $this->logger->log('success send signal {' . $signo . '} to master process');
            return true;
        }
        return false;
    }

    /**
     * 注册信号
     */
    private function registSignal()
    {
        \swoole_process::signal(SIGUSR1, function () {//用户自定义信号
            $this->showStatus();
        });
        \swoole_process::signal(SIGTERM, function () {//主进程退出信号
            $this->stopAllProcessAndExit();
        });
        \swoole_process::signal(SIGINT, function () {//终端ctrl+c退出
            $this->stopAllProcessAndExit();
        });
        \swoole_process::signal(SIGCHLD, function () {//子进程退出信号
            //必须为false，非阻塞模式
            while ($child = \swoole_process::wait(false)) {
                $pid = $child['pid'];
                $this->logger->log("[$pid]子进程退出...");
                $task_name = $this->pids[$pid]['task_name'];
                unset($this->pids[$pid]);
                --$this->topicPids[$task_name];
                if (empty($this->pids)) {
                    $this->logger->log("子进程已全部退出...");
                    if ($this->runningStatus == self::STATUS_STOP) {
                        $this->exitMaster();
                    } else {
                        $this->runningStatus = self::STATUS_WAITING;//等待状态
                    }
                }
            }
        });
    }

    /**
     * 定时检查队列长度
     */
    private function registQueueTick()
    {
        swoole_timer_tick($this->queueTickTime, function ($timer_id) {
            try {
                $message = [];
                foreach ($this->topics as $name => $topic) {
                    $this->task->openConnect($name);
                    if ($len = $this->task->len()) {
                        if($len > $this->maxQueue){
                            $message[] = '-----------------------' . PHP_EOL
                                . '队列名称：' . $name . PHP_EOL
                                . '积压长度：' . $len . PHP_EOL
                                . '检测时间：' . date('Y-m-d H:i:s') . PHP_EOL
                                . '-----------------------' . PHP_EOL;
                            $this->message->send($message);
                        }
                        //如果当前任务的子进程数不到最大值增加一个子进程
                        if ($this->topicPids[$name] < $topic['maxProcess']) {
                            $this->createProcess($name, 1);
                        }
                    }
                    $this->task->closeConnect();
                }
            } catch (\Exception $e) {
                swoole_timer_clear($timer_id);
                $this->logger->log('check queue error on line: ' . $e->getLine() . ' msg:' . $e->getMessage(), $this->logger::LEVEL_ERROR);
            } catch (\Throwable $e) {
                swoole_timer_clear($timer_id);
                $this->logger->log('check queue error on line: ' . $e->getLine() . ' msg:' . $e->getMessage(), $this->logger::LEVEL_ERROR);
            }
        });
    }

    /**
     * 进程重命名
     * @param $name
     */
    public function setProcessName($name)
    {
        //不支持mac
        if (function_exists('swoole_set_process_name') && PHP_OS != 'Darwin') {
            swoole_set_process_name($name);
        }
    }

    /**
     * 判断一个进程是否在运行状态
     * @param $pid_file
     * @return bool
     */
    public function processIsRunning($pid_file)
    {
        if (file_exists($pid_file)) {
            $info = $this->getPidInfo($pid_file);
            if (isset($info['pid']) && \swoole_process::kill($info['pid'], 0)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 保存进程信息
     * @param array $info
     * @param $file_name
     * @return bool|int
     */
    public function putPidInfo(array $info, $file_name)
    {
        return file_put_contents($file_name, \swoole_serialize::pack($info));
    }

    /**
     * 获取进程信息
     * @param $pid_file
     * @return array|mixed
     */
    public function getPidInfo($pid_file)
    {
        if (file_exists($pid_file)) {
            return \swoole_serialize::unpack(file_get_contents($pid_file));
        }
        return [];
    }

    /**
     * 记录主进程运行状态信息
     */
    private function showStatus()
    {
        $info = '---------------------------------------Bjask running status------------------------------------------------' . PHP_EOL
            . '主进程号：' . $this->masterPid . PHP_EOL
            . '主进程名：' . $this->masterProcessName . PHP_EOL
            . '当前状态：' . "\033[" . ($this->runningStatus == self::STATUS_RUNNING ? 32 : 33) . "m{$this->runningStatus}\033[0m" . PHP_EOL
            . '子进程数：' . count($this->pids) . PHP_EOL
            . '开始时间：' . date('Y-m-d H:i:s', $this->startRunTime) . PHP_EOL
            . '运行时间：' . (microtime(true) - $this->startRunTime) . 's' . PHP_EOL;
        @file_put_contents($this->pidFilePath . $this->masterStatusFile, $info);
    }

}