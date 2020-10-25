<?php
namespace TaskFlow\Libraries\TaskFlow\Model;

use Illuminate\Database\Eloquent\Model;
use TaskFlow\Libraries\TaskFlow\Model\SubTask;
use TaskFlow\Libraries\TaskFlow\Log;

class Task extends Model
{
    protected $table = 'task';

    protected $fillable = ['name', 'status'];

    public function subTasks()
    {
        return $this->hasMany(new SubTask, 'task_id', 'id');
    }

    public function finished(Task $task = null)
    {
        if ($task == null) {
            $task = $this;
        }

        $task->status = 'finished';
        $task->save();
    }

    public function pause(Task $task = null)
    {
        if ($task == null) {
            $task = $this;
        }

        $task->status = 'pause';
        $task->save();
    }

    public function running(Task $task = null)
    {
        if ($task == null) {
            $task = $this;
        }

        $task->status = 'running';
        $task->save();
    }

    public function normal(Task $task = null)
    {
        if ($task == null) {
            $task = $this;
        }

        $task->status = 'normal';
        $task->save();
    }
}
