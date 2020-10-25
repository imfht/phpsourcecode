<?php
/**
 * 任务调度器
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/12/13
 * Time: 17:30
 */

namespace Bjask;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Scheduler
{
    const MESSAGE_ERROR = 'error';
    const MESSAGE_INFO = 'info';

    private $app;
    private $message = [];
    private $taskManager;
    private $coManager;
    private $masterFilePath;
    private $masterPidFile = 'master.pid';
    private $masterStatusFile = 'master.status';
    private $masterProcessName;
    private $masterPid;
    private $writeLog = false;
    private $taskTickTime = 1000;//每秒钟检查
    private $taskMaxExecuteTime = 0;
    private $taskMaxTries = 0;
    private $disallowConcurrent = false;
    private $startRunTime = '';//开始运行时间

    public function __construct($app)
    {
        $this->app = $app;
        $this->masterFilePath = config('task.master_file_path');
        $this->writeLog = config('task.write_log');
        $this->taskMaxExecuteTime = config('task.max_execute_time');
        $this->taskMaxTries = config('task.max_tries');
        $this->disallowConcurrent = config('task.disallow_concurrent');
        if (!is_writable($this->masterFilePath)) {
            $this->setMessage("pid文件目录不可写，请修改后再试!", self::MESSAGE_ERROR);
            return;
        }
        $this->taskManager = new TaskManager($this->app);
        $this->coManager = new CoManager($this, $this->taskManager);
    }

    public function start()
    {
        $this->startMaster();
        try {
            if(!empty($this->startRunTime)){
                $this->registTaskTick();
                $this->registSignal();
                $this->coManager->checkTask();
            }
        } catch (\Throwable $e) {
            $this->stopMaster();
            $this->setMessage($e->getMessage(), self::MESSAGE_ERROR);
        }
    }

    public function stop()
    {
        try {
            $this->sendSigno(SIGTERM);
            $this->setMessage('master process stopped!');
        } catch (\Throwable $e) {
            $this->setMessage($e->getMessage(), self::MESSAGE_ERROR);
        }
    }

    public function status()
    {
        //发送用户自定义信号
        if ($this->sendSigno()) {
            sleep(1);
            $info = @file_get_contents($this->masterFilePath . DIRECTORY_SEPARATOR . $this->masterStatusFile);
            $this->setMessage($info);
            return;
        }
        $this->setMessage('获取进程状态信息失败', self::MESSAGE_ERROR);
    }

    public function reload()
    {
        //发送用户自定义信号2
        if ($this->sendSigno(SIGUSR2)) {
            $this->setMessage('Reload task succeed！');
            return;
        }
        $this->setMessage('Failed reload task', self::MESSAGE_ERROR);
    }

    public function restart()
    {
        try {
            $this->stop();
            sleep(1);
            $this->start();
        } catch (\Throwable $e) {
            $this->setMessage($e->getMessage(), self::MESSAGE_ERROR);
        }
    }

    public function startMaster()
    {
        try {
            if ($this->getPidInfo($this->masterFilePath . DIRECTORY_SEPARATOR . $this->masterPidFile)) {
                throw new \RuntimeException('A process is already running');
            }
            \swoole_process::daemon();
            $this->masterPid = getmypid();
            $this->masterProcessName = config('task.process_name');
            $this->setProcessName($this->masterProcessName);
            $this->saveMasterInfo();
            $this->startRunTime = date('Y-m-d H:i:s');
            $this->setMessage('Task process run ok!');
        } catch (\Throwable $e) {
            $this->setMessage($e->getMessage(), self::MESSAGE_ERROR);
        }

    }

    public function stopMaster()
    {
        sleep(1);
        @unlink($this->masterFilePath . DIRECTORY_SEPARATOR . $this->masterPidFile);
        @unlink($this->masterFilePath . DIRECTORY_SEPARATOR . $this->masterStatusFile);
        exit();//信号触发执行
    }

    public function sendSigno($signo = SIGUSR1)
    {
        $master_info = $this->getPidInfo($this->masterFilePath . DIRECTORY_SEPARATOR . $this->masterPidFile);
        if (!isset($master_info['pid']) || !\swoole_process::kill($master_info['pid'], 0)) {
            throw  new \RuntimeException('主进程未启动，请先启动主进程再试!');
        }
        if (\swoole_process::kill($master_info['pid'], $signo)) {
            return true;
        }
        return false;
    }

    public function registSignal()
    {
        \swoole_process::signal(SIGUSR1, function () {//用户自定义信号
            $this->showStatus();
        });
        \swoole_process::signal(SIGUSR2, function () {//用户自定义信号2
            $this->taskManager->reloadTask();
        });
        \swoole_process::signal(SIGTERM, function () {//主进程退出信号
            $this->stopMaster();
        });
        \swoole_process::signal(SIGINT, function () {//终端ctrl+c退出
            $this->stopMaster();
        });
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * 保存运行信息
     * @param $message
     * @param string $level
     * @param bool $out
     */
    public function setMessage($message, $level = self::MESSAGE_INFO, $out = true)
    {
        if (isset($this->message[$level]) && count($this->message[$level]) > 1000) $this->message[$level] = [];
        if ($out) $this->message[$level][] = $message;
        $this->writeLog && Log::info('Bjask log from ' . get_class($this) . ':' . $message);
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

    public function getMessageErrorLevel()
    {
        return self::MESSAGE_ERROR;
    }

    public function getMessageInfoLevel()
    {
        return self::MESSAGE_INFO;
    }

    /**
     * 保存当前进程信息
     */
    private function saveMasterInfo()
    {
        $info['pid'] = $this->masterPid;
        $this->putPidInfo($info, $this->masterFilePath . DIRECTORY_SEPARATOR . $this->masterPidFile);
    }

    private function registTaskTick()
    {
        swoole_timer_tick($this->taskTickTime, function ($timer_id) {
            try {
                if ($this->taskManager->hasTask) {
                    $len = $this->taskManager->taskLength();
                    for ($i = 0; $i < $len; $i++) {
                        $task = $this->taskManager->popTask();
                        if ($this->disallowConcurrent) {
                            $this->runNonConcurrent($task);
                        } else {
                            $this->coManager->runTask($task);
                        }
                    }
                } else {
                    $this->coManager->checkTask();
                }
            } catch (\Throwable $e) {
                swoole_timer_clear($timer_id);
                $this->setMessage('check task error on line: ' . $e->getLine() . ' msg:' . $e->getMessage(), self::MESSAGE_ERROR);
            }
        });
    }

    private function runNonConcurrent(Task $task)
    {
        if ($task->getRunStatus() == $this->coManager::STATUS_STOP) {
            $this->coManager->runTask($task);
        } elseif ($task->getRunStatus() == $this->coManager::STATUS_START) {
            if ((time() - $task->getStartRunTime()) > $this->taskMaxExecuteTime ||
                $task->getTries() >= $this->taskMaxTries) {
                $this->coManager->runTask($task);
            } else {
                $task->setTries();
                $this->taskManager->pushTask($task);
            }
        }
    }

    /**
     * 保存进程信息
     * @param array $info
     * @param $file_name
     * @return bool|int
     */
    private function putPidInfo(array $info, $file_name)
    {
        return @file_put_contents($file_name, serialize($info));
    }

    /**
     * 获取进程信息
     * @param $pid_file
     * @return array|mixed
     */
    private function getPidInfo($pid_file)
    {
        if (file_exists($pid_file)) {
            return unserialize(@file_get_contents($pid_file));
        }
        return false;
    }

    private function showStatus()
    {
        Carbon::setLocale('zh');
        $info[] = [
            'pid' => $this->masterPid,
            'name' => $this->masterProcessName,
            'co_num' => $this->coManager->getCoNum(),
            'len' => $this->taskManager->taskLength(),
            'start' => $this->startRunTime,
            'now' => (string)Carbon::now(),
            'persist' => Carbon::now()->diffForHumans($this->startRunTime, true)
        ];
        @file_put_contents($this->masterFilePath . DIRECTORY_SEPARATOR . $this->masterStatusFile, json_encode($info));
    }


}
