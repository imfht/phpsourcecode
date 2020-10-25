<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Thing;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThingPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Task $task        	
	 * @return bool
	 */
	public function destroy(User $user, Thing $thing) {
		return $user->id === $thing->user_id;
	}
}
