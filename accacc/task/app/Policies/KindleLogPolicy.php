<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KindleLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class KindleLogPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Task $task        	
	 * @return bool
	 */
	public function destroy(User $user, KindleLog $kindleLog) {
		return $user->id === $kindleLog->user_id;
	}
}
