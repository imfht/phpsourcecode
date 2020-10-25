<?php
/**
 * Description...
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/5/3
 * Time: 9:38
 */
require __DIR__ . '/vendor/autoload.php';
use Bjask\Task;
use Bjask\Config;
use Bjask\Logger;
use Bjask\Queue\Queue;
define('APP_NAME','app');
$config = Config::load()->get();
$logger = Logger::getInstance($config['log']);
$queue = Queue::getQueue($config['queue'], $logger);
$topic = 'mytask2';
$task = new Task($queue, $logger);
$task->openConnect($topic);
//var_dump($task->len());exit;
$task['controller'] = 'app\\Controllers\\TestController';
$task['action'] = 'index';
$task['extras'] = ['topic' => $topic, 't2' => 222];
$msg = serialize($task);
$task->add($msg);
$task['controller'] = 'app\\Controllers\\TestController';
$task['action'] = 'index';
$task['extras'] = ['topic' => $topic, 't2' => 222];
$msg = serialize($task);
$task->add($msg);
//$conn = $this->queue->createConnection('mytask2');
//$conn->push('|mytask2|testsss33eee3|');