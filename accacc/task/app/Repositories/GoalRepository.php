<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Goal;

class GoalRepository {
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user) {
		return Goal::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'asc' )->get ();
	}
	
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByStatus(User $user, string $status, $needPage) {
		$goals = Goal::where ( 'user_id', $user->id )->where ( 'status', $status );
		
		if ($needPage) {
			return $goals->paginate ( 20 );
		} else {
			return $goals->get ();
		}
	}
	
	/**
	 * Get goal for goal id.
	 *
	 * @param User $user        	
	 * @param int $goal_id        	
	 * @return Collection
	 */
	public function forGoalId(User $user, $goal_id) {
		return Goal::where ( 'user_id', $user->id )->where ( 'id', $goal_id )->get ();
	}
}
