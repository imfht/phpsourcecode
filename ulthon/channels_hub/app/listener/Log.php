<?php
declare (strict_types = 1);

namespace app\listener;

use app\common\Worker;
use think\event\LogWrite;

class Log
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle(LogWrite $event)
    {
        //

        foreach ($event->log as $level => $log_list) {
            foreach ($log_list as $key => $value) {
                Worker::safeEcho('当前进程:'.posix_getpid().':'.$level.':'.$value.PHP_EOL);
            }
        }

        return true;
    }    
}
