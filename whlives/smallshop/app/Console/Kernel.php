<?php

namespace App\Console;

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
        Commands\OrderCancel::class,
        Commands\OrderConfirm::class,
        Commands\OrderEvaluation::class,
        Commands\OrderComplete::class,
        Commands\DelOutTimeInfo::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('order_cancel')->everyMinute();//取消订单
        $schedule->command('order_confirm')->everyThirtyMinutes();//确认订单收货
        $schedule->command('order_evaluation')->everyThirtyMinutes();//默认评价订单
        $schedule->command('order_complete')->everyThirtyMinutes();//订单交易完成
        $schedule->command('del_out_time_info')->dailyAt('1:00');//删除指定信息
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
