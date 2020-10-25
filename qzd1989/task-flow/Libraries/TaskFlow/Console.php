<?php
/**
 *
 */
namespace TaskFlow\Libraries\TaskFlow;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use TaskFlow\Libraries\TaskFlow\Task;
use TaskFlow\Libraries\TaskFlow\Model\Task as TaskModel;
use TaskFlow\Libraries\TaskFlow\Model\SubTask as SubTaskModel;

class Console
{
    public static $capsule = null;

    public static function run($taskId = null)
    {
        self::capsule();
        $where = [];

        if ($taskId) {
            $where[] = ['id', '=', $taskId];
        } else {
            $where[] = ['status', '=', 'normal'];
        }

        $tasks = TaskModel::where($where)->get()->each(function ($row) {

            if ($row->status != 'normal') {
                return false;
            }

            Task::setTask($row->name);
            $subTask = $row->subTasks()->orderByDesc('id')->first();

            if (!$subTask) {
                Task::create($row, new SubTaskModel);
            } else {
                $method = $subTask->method;
                Task::$method($row, $subTask);
            }

        });
    }

    public static function capsule()
    {
        if (self::$capsule != null) {
            return self::$capsule;
        }

        $capsule        = new Capsule();
        $databaseConfig = self::getDatabaseConfig();
        $capsule->addConnection($databaseConfig);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        self::$capsule = $capsule;
    }

    public static function getDatabaseConfig()
    {
        $databaseConfig = require TASKFLOW_ROOT . 'Config/Database.php';
        return $databaseConfig['connections'][$databaseConfig['default']];
    }

}
