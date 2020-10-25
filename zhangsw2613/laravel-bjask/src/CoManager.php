<?php
/**
 * 协程管理器
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/12/19
 * Time: 14:30
 */

namespace Bjask;

use Swoole\Coroutine as co;

class CoManager
{
    const STATUS_STOP = 0;
    const STATUS_START = 1;
    const STATUS_DONE = 2;
    const STATUS_ERROR = 3;

    private $scheduler;
    private $taskManager;
    private $pools = ['check' => [], 'run' => []];

    public function __construct(Scheduler $scheduler, TaskManager $taskManager)
    {
        $this->scheduler = $scheduler;
        $this->taskManager = $taskManager;
    }

    public function checkTask()
    {
        if (empty($this->pools['check'])) {
            $cid = co::create(function () {
                while (true) {
                    $this->taskManager->resolveTask();
                    $this->scheduler->setMessage('Done resolve task!', $this->scheduler::MESSAGE_INFO, false);
                    co::sleep(1);
                }
                defer(function () {
                    $this->deferDel(co::getuid(), 'check');
                });
            });
            $this->setContext('check', $cid);
        } else {
            $this->scheduler->setMessage('Another check task coroutine is already exist!', $this->scheduler::MESSAGE_ERROR, false);
        }
    }

    /**
     * 子协程中执行任务
     * @param Task $task
     */
    public function runTask(Task $task)
    {
        $cid = co::create(function () use ($task) {
            try {
                $task->setRunStatus(self::STATUS_START);
                $task->setStartRunTime();
                $task->setTries(1);
                $task->run();
                co::sleep(0.1);
                defer(function () use ($task) {
                    $this->deferDel(co::getuid(), 'run');
                    if ($task->isRepeat === false) {
                        $this->taskManager->removeTask($task);
                    } else {
                        $task->setRunStatus(self::STATUS_STOP);
                    }
                });
            } catch (\Throwable $e) {
                $this->scheduler->setMessage($e->getMessage(), $this->scheduler::MESSAGE_ERROR, false);
            }

        });
        $this->setContext('run', $cid);
    }

    public function getCoNum()
    {
        return 'check:[' . count($this->pools['check']) . '] run:[' . count($this->pools['run']) . ']';
    }

    private function setContext($key, $cid)
    {
        if ($cid) {
            $this->pools[$key][$cid] = $cid;
        }
    }

    private function deferDel($cid, $key = 'run')
    {
        if ($cid) {
            unset($this->pools[$key][$cid]);
            $this->scheduler->setMessage("{$key}协程【{$cid}】已释放!", $this->scheduler::MESSAGE_INFO, false);
        }
    }

}