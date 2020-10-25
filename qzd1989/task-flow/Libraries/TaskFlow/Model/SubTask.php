<?php
namespace TaskFlow\Libraries\TaskFlow\Model;

use Illuminate\Database\Eloquent\Model;
use TaskFlow\Libraries\TaskFlow\Log;

class SubTask extends Model
{
    protected $table = 'task_sub';

    protected $fillable = ['task_id', 'method', 'data', 'status', 'max_times', 'retry_times'];

    public static function add($taskId, $method, $data = [], $status = 'waiting', $maxTimes = 5, $retryTimes = 0)
    {
        $query = [
            'task_id'     => $taskId,
            'method'      => $method,
            'data'        => json_encode($data),
            'status'      => $status,
            'max_times'   => $maxTimes,
            'retry_times' => $retryTimes,
        ];

        return self::create($query);
    }

    public function running(SubTask $subTask = null)
    {
        if ($subTask == null) {
            $subTask = $this;
        }

        $subTask->status = 'running';
        $subTask->save();
    }

    public function pause(SubTask $subTask = null)
    {
        if ($subTask == null) {
            $subTask = $this;
        }

        $subTask->status = 'pause';
        $subTask->save();
    }

    public function finished(SubTask $subTask = null)
    {
        if ($subTask == null) {
            $subTask = $this;
        }

        $subTask->status = 'finished';
        $subTask->save();
    }

    public function retry(SubTask $subTask = null)
    {
        if ($subTask == null) {
            $subTask = $this;
        }

        $times                = $subTask->retry_times += 1;
        $subTask->retry_times = $times;
        $subTask->save();
    }
}
