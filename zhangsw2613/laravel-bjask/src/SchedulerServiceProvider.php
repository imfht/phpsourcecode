<?php

namespace Bjask;

use Bjask\Console\TaskManagerCommand;
use Illuminate\Support\ServiceProvider;

class SchedulerServiceProvider extends ServiceProvider
{

    protected $defer = true;

    public function boot()
    {
        $path = realpath(__DIR__ . '/Config/task.php');

        $this->mergeConfigFrom($path, 'task');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('scheduler', function ($app) {
            return new  Scheduler($app);
        });
        $this->registerCommand();
        $this->commands('task.manage.command');
    }

    public function registerCommand()
    {
        $this->app->singleton('task.manage.command', function ($app) {
            return new TaskManagerCommand;
        });
    }

    public function provides()
    {
        return [
            'scheduler'
        ];
    }
}
