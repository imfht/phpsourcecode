<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Repositories\TaskRepository;
use DB;
use App\Models\Note;
use App\Task;
use App\Pomo;
use App\Statistics;
use App\Repositories\FeedRepository;
use App\Feed;
use App\Article;
use App\Http\Utils\SpideUtil;
use App\Models\Setting;
use App\KindleLog;
use App\ArticleSub;
use App\Repositories\SettingRepository;
use Develpr\Phindle\Phindle;
use Develpr\Phindle\Content;
use Develpr\Phindle\OpfRenderer;

class Kernel extends ConsoleKernel {
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
			// Commands\Inspire::class,
			Commands\FanfouPublish::class,
			Commands\FeedCommon::class,
			Commands\FeedType2::class,
			Commands\FeedType3::class,
			Commands\KindlePush::class,
			Commands\StatisticsCron::class,
			Commands\TaskReminder::class 
	];
	
	/**
	 * Define the application's command schedule.
	 *
	 * @param \Illuminate\Console\Scheduling\Schedule $schedule        	
	 * @return void
	 */
	protected function schedule(Schedule $schedule) {
		date_default_timezone_set ( "Asia/Shanghai" );
		
		$schedule->command ( 'fanfou_publish' )->daily ();
		$schedule->command ( 'task_reminder' )->everyMinute ();
		$schedule->command ( 'statistics_cron' )->dailyAt ( '00:30' );
		$schedule->command ( 'feed_common', array (
				1 
		) )->everyTenMinutes ();
		$schedule->command ( 'feed_common', array (
				2 
		) )->hourly ();
		$schedule->command ( 'feed_common', array (
				3 
		) )->daily ();
		$schedule->command ( 'feed_common', array (
				4 
		) )->daily ();
		$schedule->command ( 'kindle_push' )->dailyAt ( '18:00' );
		
		$schedule->command ( 'backup2qiniu', array (
				env ( 'TASK_SQL_FILE_PATH' ) 
		) )->dailyAt ( '18:00' );
		$schedule->command ( 'backup2qiniu', array (
				env ( 'WWW_SQL_FILE_PATH' ) 
		) )->dailyAt ( '18:00' );
	}
	
	/**
	 * Register the commands for the application.
	 *
	 * @return void
	 */
	protected function commands() {
		$this->load ( __DIR__ . '/Commands' );
		
		require base_path ( 'routes/console.php' );
	}
}
