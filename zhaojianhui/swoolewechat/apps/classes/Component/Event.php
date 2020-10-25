<?php

namespace App\Component;

use Swoole\Exception;

/**
 * 自定义事件类.
 */
class Event extends \Swoole\Component\Event
{
    public $workerNum;
    private $isStop = 0;
    private $pidFile;
    private $daemon = false;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->_atomic = new \swoole_atomic(1);
    }

    /**
     * 设置进程保存文件.
     *
     * @param $pidFile
     */
    public function setPidFile($pidFile)
    {
        $this->pidFile = $pidFile;
    }

    /**
     * 保存pid到文件.
     *
     * @param $pidList
     */
    public function savePidList($pidList)
    {
        //写入pid列表到pid文件
        if ($pidList) {
            $lastPidList             = $this->getPidList();
            $lastPidList && $pidList = array_merge($lastPidList, $pidList);
            file_put_contents($this->pidFile, json_encode($pidList));
        }
    }

    /**
     * 获取已有pid列表.
     */
    public function getPidList()
    {
        $serverPid = [];
        //合并已有的pid列表
        if (file_exists($this->pidFile)) {
            $serverPid               = file_get_contents($this->pidFile);
            $serverPid && $serverPid = json_decode($serverPid, true);
        }

        return $serverPid;
    }

    /**
     * 删除pid列表.
     */
    public function delPidList()
    {
        if (!file_exists($this->pidFile)) {
            throw new \Exception('你无法停止未启动的服务');

            return false;
        }
        //删除进程文件
        unlink($this->pidFile);
    }

    /**
     * 重构事件.
     *
     * @param int  $worker_num
     * @param bool $daemon
     */
    public function runWorker($worker_num = 0, $daemon = false)
    {
        if (file_exists($this->pidFile)) {
            throw new \Exception('服务已经启动');

            return false;
        }
        if (is_numeric($worker_num) && $worker_num > 0) {
            $this->workerNum = $worker_num;
        }
        if (is_bool($daemon)) {
            $this->daemon = $daemon;
        }
        $this->isStop = 0;
        if ($this->workerNum > 1 or $daemon) {
            if (!class_exists('\swoole\process')) {
                throw new Exception\NotFound('require swoole extension');
            }
            if ($this->workerNum < 0 or $this->workerNum > 1000) {
                $this->workerNum = 200;
            }
        } else {
            $this->_atomic->set(1);
            $this->_worker();

            return;
        }

        if ($this->daemon) {
            \swoole_process::daemon();
        }

        $this->_atomic->set(1);
        for ($i = 0; $i < $this->workerNum; $i++) {
            $process          = new \swoole\process([$this, '_worker'], false, false);
            $pidList[]        = $process->start();
            $this->_workers[] = $process;
        }
        //写入pid列表到pid文件
        $this->savePidList($pidList);
        /**
         * 如果为守护进程，则子进程自动重启.
         */
        $isStop = $this->isStop;
        \swoole_process::signal(SIGCHLD, function ($isStop) {
            while (true) {
                $exitProcess = \swoole_process::wait(false);
                if ($exitProcess) {
                    //写入pid列表到pid文件
                    $pidList = [];
                    foreach ($this->_workers as $k => $p) {
                        if ($p->pid == $exitProcess['pid']) {
                            if ($isStop == 0 && $this->_atomic->get() == 1) {
                                $pidList[] = $p->start();
                            } else {
                                unset($this->_workers[$k]);
                                if (count($this->_workers) == 0) {
                                    swoole_event_exit();
                                }
                            }
                        } else {
                            $pidList[] = $p->pid;
                        }
                    }
                    //写入pid列表到pid文件
                    if ($isStop == 0 && $pidList) {
                        $this->savePidList($pidList);
                    }
                } else {
                    break;
                }
            }
        });

        //监听主进程的退出信号，然后退出所有子进程
        \swoole_process::signal(SIGTERM, function () {
            //停止运行
            $this->_atomic->set(0);
            //关闭所有子进程
            foreach ($this->_workers as $k => $p) {
                \swoole_process::kill($p->pid);
            }
            //删除进程文件
            $this->delPidList();
        });
    }

    /**
     * 停止运行.
     */
    public function stopWorker()
    {
        $serverPid = $this->getPidList();
        if ($serverPid) {
            foreach ($serverPid as $pid) {
                \swoole_process::kill($pid);
            }
            $this->isStop = 1;
        }
        //删除进程文件
        $this->delPidList();
    }
}
