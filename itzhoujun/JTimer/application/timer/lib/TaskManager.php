<?php
/**
 * Created by PhpStorm.
 * User: zhoujun
 * Date: 2018/3/31
 * Time: 13:10
 */

namespace app\timer\lib;


use think\Log;

class TaskManager
{
    const LOG_KEY = 'cmd_cron_task_log';
    const TASK_KEY = 'cmd_cron_task_list';
    const TASK_IS_CHANGE = 'cmd_cron_task_is_change';

    /**
     * @param $tasks
     * @return bool 是否有写到缓存中
     * 如果任务内容改变，则重新写到缓存中
     */
    public static function loadTask($tasks){
        $old_task = self::getTasks();
        $old_name = md5(serialize($old_task));
        $md5_name = md5(serialize($tasks));
        if($old_name != $md5_name){
            cache(self::TASK_KEY,$tasks);
            return true;
        }
        return false;
    }


    public static function isChange($tag = null){
        if(is_null($tag)){
            return cache(self::TASK_IS_CHANGE);
        }
        cache(self::TASK_IS_CHANGE,$tag);
    }

    /**
     * @return mixed
     * 读取缓存中的任务列表
     */
    public static function getTasks(){
        return cache(self::TASK_KEY);
    }

    /**
     * @param $tasks
     * 将任务直接写到缓存中
     */
    public static function setTasks($tasks){
        cache(self::TASK_KEY,$tasks);
    }

    /**
     * @param $cron_task
     * @param $spend_time
     * 将日志写到缓存中
     */
    public static function log($cron_task,$create_time,$spend_time){
        $logs = self::getLogs();
        $logs[] = [
            'ct_id' => $cron_task['id'],
            'cmd' => $cron_task['cmd'],
            'create_time' => $create_time,
            'spend_time' => $spend_time
        ];
        cache(self::LOG_KEY,$logs);
    }

    /**
     * @param $task
     * 执行任务，并记录日志
     */
    public static function exec($task){
        $start_time = microtime(true);
        $create_time = date('Y-m-d H:i:s');
        exec($task['cmd']);
        $end_time = microtime(true);
        $spend_time = $end_time - $start_time;
        TaskManager::log($task,$create_time,$spend_time);
    }

    /**
     * @return mixed
     * 获取所有日志
     */
    public static function getLogs(){
        return cache(self::LOG_KEY);
    }

    /**
     * 清除缓存中所有日志
     */
    public static function clearLogs(){
        cache(self::LOG_KEY,[]);
    }

    /**
     * 将任务标记为无
     */
    public static function clear(){
        self::loadTask([]);
    }
}