<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;

/**
 * 待办事项业务逻辑
 *
 * @author edison.an
 *        
 */
class TaskService {
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user, $needPage) {
		$task = Task::where ( 'user_id', $user->id )->orderBy ( 'updated_at', 'desc' );
		if ($needPage) {
			return $task->paginate ( 50 );
		} else {
			return $task->get ();
		}
	}
	
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByStatus(User $user, string $status) {
		return Task::with ( 'goal' )->where ( 'user_id', $user->id )->where ( 'status', $status )->orderBy ( 'is_top', 'desc' )->orderBy ( 'priority', 'desc' )->orderBy ( 'updated_at', 'desc' )->get ();
	}
	public function forUserByRemindTime($start_time, $end_time) {
		return Task::where ( 'remindtime', '>', $start_time )->where ( 'remindtime', '<', $end_time )->where ( 'status', 1 )->orderBy ( 'priority', 'desc' )->orderBy ( 'updated_at', 'desc' )->get ();
	}
	public function forUserByUserIdRemindTime($user_id, $start_time, $end_time) {
		return Task::where ( 'user_id', $user_id )->where ( 'remindtime', '>', $start_time )->where ( 'remindtime', '<', $end_time )->where ( 'status', 1 )->get ();
	}
}
