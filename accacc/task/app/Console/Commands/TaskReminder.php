<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\TaskRepository;
use Mail;
use App\Http\Utils\CommonUtil;

/**
 * reminder the deadline task
 *
 * @author edison.an
 *        
 */
class TaskReminder extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'task_reminder';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Task Reminder';
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$taskRepository = new TaskRepository ();
		
		$start_time = date ( 'Y-m-d H:i:s' );
		$end_time = date ( 'Y-m-d H:i:s', strtotime ( $start_time ) + 60 );
		
		// get need remind task list
		$tasks = $taskRepository->forUserByRemindTime ( $start_time, $end_time );
		foreach ( $tasks as $task ) {
			$user = $task->user;
			// 邮件通知
			Mail::send ( 'emails.reminder', [ 
					'user' => $user,
					'task' => $task 
			], function ($m) use ($user, $task) {
				$m->to ( $user->email, $user->name )->subject ( '[待办提醒]' . $task->name );
			} );
			// ifttt通知
			if (isset ( $task->user->setting->ifttt_notify )) {
				CommonUtil::iftttnotify ( '待办提醒', $task->name, 'https://task.congcong.us', $task->user->setting->ifttt_notify );
			}
		}
		
		$tasks = $taskRepository->forUserByDeadline ( $start_time, $end_time );
		foreach ( $tasks as $task ) {
			$user = $task->user;
			// 邮件通知
			Mail::send ( 'emails.reminder', [ 
					'user' => $user,
					'task' => $task 
			], function ($m) use ($user, $task) {
				$m->to ( $user->email, $user->name )->subject ( '[待办提醒]' . $task->name );
			} );
			// ifttt通知
			if (isset ( $task->user->setting->ifttt_notify )) {
				CommonUtil::iftttnotify ( '待办截止提醒', $task->name, 'https://task.congcong.us', $task->user->setting->ifttt_notify );
			}
		}
	}
}
