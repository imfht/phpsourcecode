<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/26
 * Time: 14:46
 */
namespace app\timer\command;

use app\common\cron\CronExpression;
use app\timer\lib\TaskManager;
use app\timer\lib\Timer;
use app\timer\lib\TimingWheel;
use app\timer\lib\Worker;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Exception;
use think\Log;

class JTimerCommand extends Command
{
    protected function configure()
    {
        $this->setName('jtimer')->setDescription('usege: start | stop | status');
        $this->addArgument('action',Argument::REQUIRED);
        $this->addOption('deamonize','-d',Option::VALUE_NONE);
    }

    protected function execute(Input $input, Output $output)
    {
        $this->checkEnv();
        global $argv;
        $argv[0] = $input->getArgument('action');
        $argv[1] = $input->getOption('deamonize') ? '-d' : '';
        $worker = new Worker();
        $worker->onTask = function($worker,$data){
            Log::info('开始执行任务'.print_r($data,true));
            TaskManager::exec($data);
        };
        global $now_timestamp;
        $now_timestamp = time();
        $worker->onWorkerStart = function(Worker $worker){
            TaskManager::clear();
            if($worker->worker_name == 'db-worker'){
                Timer::add(5,function() use ($worker){
                    //将数据库中的任务写到缓存中
                    $list = Db::name('cron_task')->where('status',1)->select();
                    if(TaskManager::loadTask($list)){
                        TaskManager::isChange(true);
                    }
                    //将缓存中的日志写到数据库中
                    $log_list = TaskManager::getLogs();
                    if(!empty($log_list)){
                        try{
                            Db::name('cron_task_log')->insertAll($log_list);
                            TaskManager::clearLogs();
                        }catch (Exception $e){
                            Log::info('log err >' . $e->getMessage());
                        }
                    }

                    //清除之前的旧日志
                    $day = getSetting('cron_task_log_save_day');
                    $key = 'has_delete_log_'.date('Y-m-d') . '_'.$day;
                    if($day > 0 && cache($key) != true){ //清除日志一天只需执行一次即可
                        Db::name('cron_task_log')
                            ->where('create_time','<',date('Y-m-d 00:00:00',strtotime("-$day day")))
                            ->delete();
                        cache($key,true,24*60*60);
                    }

                });
            }elseif($worker->worker_name == 'timer-worker'){
                $worker->wheel = new TimingWheel();
                TaskManager::isChange(true);
                Timer::add(1,function() use ($worker){
                    $now = Timer::$now_time;
                    if(TaskManager::isChange()){
                        $tasks = TaskManager::getTasks();
                        TaskManager::isChange(false);
                        Log::info('is change:' . print_r($tasks,true));
                        $worker->wheel->clear();
                        if(!empty($tasks)){
                            foreach ($tasks as $task){
                                $next_run_time = CronExpression::getNextRunTime($task['cron_expression'],$now);
                                $interval = strtotime($next_run_time) - $now;
                                $worker->wheel->add($interval,$task);
                            }
                        }

                    }

                    //从文件中读取要执行的任务
                    $list = $worker->wheel->popSlots();
                    if(!empty($list)){
                        Log::info('发现要执行的任务'.count($list).'个');
                        foreach ($list as $task){
                            $next_run_time = CronExpression::getNextRunTime($task['cron_expression'],$now);
                            Log::info('当前时间：'.date('Y-m-d H:i:s',$now));
                            Log::info('下次执行：'.$next_run_time);
                            $interval = strtotime($next_run_time) - $now;
                            Log::info('$interval：'.$interval);
                            $worker->wheel->add($interval,$task);
                            $worker->task($task);
                        }
                    }

                });

            }
            \app\timer\lib\Timer::tick();

        };
        Worker::runAll();
    }

    public function checkEnv(){
        if(!function_exists('exec')){
            exit('请修改php.ini文件，开放exec方法'."\n");
        }
    }
}