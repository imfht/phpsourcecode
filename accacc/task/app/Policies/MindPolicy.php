<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Mind;
use Illuminate\Auth\Access\HandlesAuthorization;

class MindPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Mind $mind        	
	 * @return bool
	 */
	public function destroy(User $user, Mind $mind) {
		return $user->id === $mind->user_id;
	}
}
