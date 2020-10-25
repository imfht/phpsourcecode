<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FeedSub;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedSubPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Task $task        	
	 * @return bool
	 */
	public function destroy(User $user, FeedSub $feedSub) {
		return $user->id === $feedSub->user_id;
	}
}
