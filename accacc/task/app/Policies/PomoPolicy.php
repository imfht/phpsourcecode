<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pomo;
use Illuminate\Auth\Access\HandlesAuthorization;

class PomoPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given pomo.
	 *
	 * @param User $user        	
	 * @param Task $pomo        	
	 * @return bool
	 */
	public function destroy(User $user, Pomo $pomo) {
		return $user->id === $pomo->user_id;
	}
}
