<?php
namespace Naka507\Socket;
use Exception;
class Timer
{

    protected static $_tasks = array();

    protected static $_event = null;

    public static function init($event = null)
    {
        if ($event) {
            self::$_event = $event;
        } else {
            if (function_exists('pcntl_signal')) {
                pcntl_signal(SIGALRM, array('\Naka507\Socket\Timer', 'signalHandle'), false);
            }
        }
    }

    public static function signalHandle()
    {
        if (!self::$_event) {
            pcntl_alarm(1);
            self::tick();
        }
    }

    public static function add($time_interval, $func, $args = array(), $persistent = true)
    {
        if ($time_interval <= 0) {
            Server::console(new Exception("bad time_interval"));
            return false;
        }

        if (self::$_event) {
            return self::$_event->add($time_interval,
                $persistent ? Events::EV_TIMER : Events::EV_TIMER_ONCE, $func, $args);
        }

        if (!is_callable($func)) {
            Server::console(new Exception("not callable"));
            return false;
        }

        if (empty(self::$_tasks)) {
            pcntl_alarm(1);
        }

        $time_now = time();
        $run_time = $time_now + $time_interval;
        if (!isset(self::$_tasks[$run_time])) {
            self::$_tasks[$run_time] = array();
        }
        self::$_tasks[$run_time][] = array($func, (array)$args, $persistent, $time_interval);
        return 1;
    }

    public static function tick()
    {
        if (empty(self::$_tasks)) {
            pcntl_alarm(0);
            return;
        }

        $time_now = time();
        foreach (self::$_tasks as $run_time => $task_data) {
            if ($time_now >= $run_time) {
                foreach ($task_data as $index => $one_task) {
                    $task_func     = $one_task[0];
                    $task_args     = $one_task[1];
                    $persistent    = $one_task[2];
                    $time_interval = $one_task[3];
                    try {
                        call_user_func_array($task_func, $task_args);
                    } catch (\Exception $e) {
                        Server::console($e);
                    }
                    if ($persistent) {
                        self::add($time_interval, $task_func, $task_args);
                    }
                }
                unset(self::$_tasks[$run_time]);
            }
        }
    }

    public static function del($timer_id)
    {
        if (self::$_event) {
            return self::$_event->del($timer_id, Events::EV_TIMER);
        }

        return false;
    }

    public static function delAll()
    {
        self::$_tasks = array();
        pcntl_alarm(0);
        if (self::$_event) {
            self::$_event->clearAllTimer();
        }
    }
}
