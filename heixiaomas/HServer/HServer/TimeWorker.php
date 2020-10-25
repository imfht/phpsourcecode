<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/9/12
 * Time: 17:46
 */

namespace HServer;

require_once __DIR__ . '/../vendor/wokerman/workerman/Autoloader.php';

use Workerman\Worker;
use \Workerman\Lib\Timer;

class TimeWorker extends Worker
{
    public function __construct($socket_name = '', array $context_option = array())
    {
        parent::__construct($socket_name, $context_option);
    }

    public function onClientStart($worker)
    {
        $path = __DIR__ . "/../app/task/";
        $filterFile = scandir($path);
        foreach ($filterFile as $filename) {
            if ($filename != '.' && $filename != '..' && $filename . strpos($filename, 'php') !== false) {
                $classname = substr($filename, 0, -4);

                $class = new \ReflectionClass($classname);
                $timeTask = $class->newInstanceArgs();
                if ($class->hasMethod("run")) {
                    $time = $class->getProperty('time');
                    $time->setAccessible(true);
                    $timeout = $time->getValue($timeTask);
                    $run = $class->getMethod("run");
                    $run->setAccessible(true);
                    Timer::add($timeout, function () use ($run, $timeTask) {
                        $run->invoke($timeTask);
                    });
                } else {
                    echo "无定时器";
                }
            }
        }
    }

    public function run()
    {
        Worker::$logFile = __DIR__ . '/../log/task.log';
        Worker::$stdoutFile = __DIR__ . '/../log/task_stdout.log';
        $this->onWorkerStart = array($this, 'onClientStart');
        parent::run();
    }


}