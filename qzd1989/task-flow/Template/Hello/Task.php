<?php
/**
 * 任务类示例
 */
namespace TaskFlow\Template\Hello;

use TaskFlow\Libraries\TaskFlow\InterfaceTask;

use TaskFlow\Libraries\TaskFlow\Model\Task as TaskModel;
use TaskFlow\Libraries\TaskFlow\Model\SubTask as SubTaskModel;

class Task implements InterfaceTask
{
    /**
     * 创建任务
     */
    public function create(TaskModel $task, SubTaskModel $subTask)
    {
        $data = ['str' => 'say hello'];
        return SubTaskModel::add($task->id, 'saySomething', $data);
    }

    /**
     * 执行任务子节点
     */
    public function saySomething(TaskModel $task, SubTaskModel $subTask)
    {
        print_r($subTask->data . PHP_EOL);
        $res = ['action' => 'do love me'];
        return SubTaskModel::add($task->id, 'doSomething', $res);
    }

    /**
     * 执行任务子节点
     * 返回false即表示为任务结束
     */
    public function doSomething(TaskModel $task, SubTaskModel $subTask)
    {
        print_r($subTask->data . PHP_EOL);
        return false;
    }
}
