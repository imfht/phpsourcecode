<?php
/**
 * Description...
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/5/3
 * Time: 9:39
 */
require __DIR__ . '/vendor/autoload.php';
use Bjask\Task;
use Bjask\Config;
use Bjask\Logger;
use Bjask\Queue\Queue;
define('APP_NAME','app');
//测试用例
$config = Config::load()->get();
$logger = Logger::getInstance($config['log']);
$queue = Queue::getQueue($config['queue'], $logger);
$task = new Task($queue, $logger);
$process = new \swoole_process(function (\swoole_process $process) use ($task) {
    $task->openConnect('mytask1');
    $task->run();
    //$this->logger->log("topic_name:$topic--res:".var_export($res,true));//数据格式：|topic_name||test|
    $task->closeConnect();
});
$pid = $process->start();
exit;

$task = new Task($queue, $logger);
$topics = ['mytask1', 'mytask2'];
/* $topic = 'mytask1';
 $process = new \swoole_process(function (\swoole_process $process) use($task,$topic) {
     swoole_set_process_name('php: bjask');
     $begin_time = microtime(true);
     $maxExecuteTime = 100;
     $task->openConnect($topic);
     $res = $task->run();
     $this->logger->log("topic_name:$topic--res:".var_export($res,true));//数据格式：|topic_name||test|
     $task->closeConnect();
 });
 $pid = $process->start();*/
foreach ($topics as $topic) {
    for ($i = 0; $i < 2; $i++) {
        $process = new \swoole_process(function (\swoole_process $process) use ($task, $topic) {
            swoole_set_process_name('php: bjask');
            $begin_time = microtime(true);
            $maxExecuteTime = 100;
            $task->openConnect($topic);
            $task->run();
            //$this->logger->log("topic_name:$topic--res:".var_export($res,true));//数据格式：|topic_name||test|
            $task->closeConnect();
        });
        $pid = $process->start();
    }
}