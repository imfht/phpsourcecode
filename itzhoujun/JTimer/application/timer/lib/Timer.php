<?php


    /**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/21 0021
 * Time: 16:32
 */

namespace app\timer\lib;
use Exception;

class Timer
{
    public static $now_time;
    public static $tasks = array();
    public static $task_worder_num = 30;

    public static function init()
    {
        pcntl_signal(SIGALRM, array( __CLASS__, 'signalHandle'), false);
    }

    public static function signalHandle()
    {
        pcntl_alarm(1);
        static::$now_time++;
        //执行任务
        if (empty(self::$tasks)) {
            return;
        }
        foreach (self::$tasks as $run_time => $task) {
            $time_now = time();
            if ($time_now >= $run_time) {
                $func = $task[0];
                $args = $task[1];
                $interval = $task[2];
                $persistent = $task[3];
                call_user_func_array($func, $args);
                unset(self::$tasks[$run_time]);
                if($persistent){
                    Timer::add($interval, $func, $args,$persistent);
                }
            }
        }
    }


    public static function add($interval, $func, $args = array(),$persistent = true)
    {
        if ($interval <= 0) {
            echo new Exception('wrong interval');
            return false;
        }
        if (!is_callable($func)) {
            echo new Exception('not callable');
            return false;
        } else {
            $runtime = time() + $interval;
            self::$tasks[$runtime] = array($func, $args, $interval,$persistent);
            return true;
        }
    }

    public static function tick()
    {
        static::$now_time = time();
        pcntl_alarm(1);
    }
}