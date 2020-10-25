<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Third;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThirdPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Third $third        	
	 * @return bool
	 */
	public function destroy(User $user, Third $third) {
		return $user->id === $third->user_id;
	}
}
