<?php
/**
 * 任务抽象类
 */
namespace TaskFlow\Libraries\TaskFlow;

use TaskFlow\Libraries\TaskFlow\Model\SubTask;
use Illuminate\Database\Capsule\Manager as Capsule;
use TaskFlow\Libraries\TaskFlow\Log;

class Task
{
    public static $task;

    public static function setTask($name)
    {
        $taskClass  = 'TaskFlow\\Template\\' . $name . '\\Task';
        self::$task = new $taskClass;
        return self::$task;
    }

    public static function getTask()
    {
        return self::$task;
    }

    public static function __callStatic($name, $arguments)
    {
        $taskObject = self::getTask();
        $task       = $arguments[0];
        $subTask    = $arguments[1];

        Log::debug('task start ' . $task->name, ['id' => $task->id]);

        Capsule::beginTransaction();

        $task->running();

        if ($name != 'create' && $subTask->retry_times >= $subTask->max_times) {

            Log::debug('task pause ' . $task->name, ['id' => $task->id, 'times' => $subTask->max_times]);

            $result = $task->pause() && $subTask->pause();

            Capsule::commit();

            return $result;
        }

        try {

            if ($name != 'create') {
                $subTask->running();
            }

            $result = $taskObject->$name($task, $subTask);

            if ($name != 'create') {
                $subTask->finished();
            }

            $task->normal();

            $subTaskModel = clone $subTask;

            $where = [
                ['task_id', '=', $task->id],
            ];

            if (!$subTaskModel->where($where)->whereIn('status', ['waiting', 'running'])->first()) {
                $task->finished();
            }

            Capsule::commit();

        } catch (\Exception $e) {

            Log::error($e);

            Capsule::rollBack();

            if ($name != 'create') {
                $subTask->retry();
            }
        }

        Log::debug('task end ' . $task->name, ['id' => $task->id]);

    }
}
