<?php

namespace App\Console;

use App\Console\Commands\CheckImage;
use App\Console\Commands\CheckQueue;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        CheckImage::class,
        CheckQueue::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('check:image')
            ->daily();

        $schedule->command('check:queue')
            ->everyFiveMinutes();
    }
}
