<?php
/**
 * 任务管理类
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/12/13
 * Time: 17:30
 */

namespace Bjask;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class TaskManager
{
    private $app;
    public $tasks = [];
    public $hasTask = false;
    public $taskQueue;
    public $namespace = '';

    public function __construct($app)
    {
        $this->app = $app;
        $this->taskQueue = new \SplQueue();
        $this->loadTasks();
        return $this;
    }

    /**
     * 解析任务
     * @return $this
     */
    public function resolveTask()
    {
        //解析执行时间
        foreach ($this->dueTasks() as $task) {
            $this->pushTask($task);
        }
        if ($this->taskQueue->isEmpty()) {
            $this->hasTask = false;
        } else {
            $this->hasTask = true;
        }
        return $this;
    }

    /**
     * 筛选出所有到期任务
     * @return mixed
     */
    public function dueTasks()
    {
        return collect($this->tasks)->filter->isDue();
    }

    public function pushTask($task)
    {
        $this->taskQueue->enqueue($task);
    }

    public function popTask()
    {
        if (!$this->taskQueue->isEmpty()) {
            $task = $this->taskQueue->dequeue();
            if ($this->taskQueue->isEmpty()) {
                $this->hasTask = false;
            }
            return $task;
        }
        $this->hasTask = false;
        return null;
    }

    public function taskLength()
    {
        return count($this->taskQueue);
    }

    public function removeTask($task)
    {
        $task = '\\' . get_class($task);
        if (isset($this->tasks[$task])) unset($this->tasks[$task]);
    }

    public function reloadTask()
    {
        $this->tasks = [];
        $this->loadTasks();
    }

    private function loadTasks()
    {
        $this->namespace = config('task.task_namespace');
        foreach ((new Finder)->in($this->namespace)->files() as $task) {
            $task = '\\' . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after(ucfirst($task->getPathname()), app_path() . DIRECTORY_SEPARATOR)
                );
            if (is_subclass_of($task, Task::class) && !(new \ReflectionClass($task))->isAbstract()) {
                $this->tasks[$task] = $this->app->make($task);
                $this->tasks[$task]->prepare();
            }
        }
    }
}