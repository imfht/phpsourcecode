<?php
/**
 * 任务类接口
 */
namespace TaskFlow\Libraries\TaskFlow;

use TaskFlow\Libraries\TaskFlow\Model\Task as TaskModel;
use TaskFlow\Libraries\TaskFlow\Model\SubTask as SubTaskModel;

interface InterfaceTask
{
    public function create(TaskModel $task, SubTaskModel $subTask);
}
