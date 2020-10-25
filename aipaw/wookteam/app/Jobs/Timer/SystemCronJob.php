<?php

namespace App\Jobs\Timer;

use App\Tasks\AutoArchivedTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

class SystemCronJob extends CronJob
{
    protected $i = 0;

    public function interval()
    {
        return 60000;    // 每60秒运行一次
    }

    public function isImmediate()
    {
        return true;    // 是否立即执行第一次，false则等待间隔时间后执行第一次
    }

    public function run()
    {
        $this->i++;
        $autoArchivedTask = new AutoArchivedTask();
        Task::deliver($autoArchivedTask);
    }
}
