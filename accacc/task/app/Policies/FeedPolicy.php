<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Feed;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Goal $goal        	
	 * @return bool
	 */
	public function destroy(User $user, Feed $feed) {
		return $user->id === $feed->user_id;
	}
}
