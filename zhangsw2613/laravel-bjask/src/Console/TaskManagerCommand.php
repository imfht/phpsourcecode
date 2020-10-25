<?php

namespace Bjask\Console;

use Bjask\Facades\Scheduler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TaskManagerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:manage {operate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the task manager';

    protected $operates = ['start', 'stop', 'restart', 'status','reload'];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $operate = $this->argument('operate');
        try {
            if (!in_array($operate, $this->operates)) {
                throw new \InvalidArgumentException('Operation not supported!');
            }
            call_user_func([Scheduler::class,$operate]);
            $message = Scheduler::getMessage();
            if ($operate == 'status') {
                if (isset($message[Scheduler::getMessageInfoLevel()]) && $info = json_decode($message[Scheduler::getMessageInfoLevel()][0], true)) {
                    $headers = ['主进程号', '主进程名', '子协程数', '积压任务数', '开始时间', '当前时间', '运行时间'];
                    $this->table($headers, $info);
                } else {
                    $this->info($message[Scheduler::getMessageErrorLevel()][0]);
                }
            } else {
                foreach ($message as $level => $msg) {
                    if ($level == Scheduler::getMessageErrorLevel() && !empty($msg)) {
                        $this->error(implode(' ' . PHP_EOL, $msg));
                    } elseif ($level == Scheduler::getMessageInfoLevel() && !empty($msg)) {
                        $this->info(implode(' ' . PHP_EOL, $msg));
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
        }

    }
}
