<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Thing;

class ThingRepository {
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user, $needPage) {
		$thing = Thing::where ( 'user_id', $user->id )->orderBy ( 'updated_at', 'desc' );
		if ($needPage) {
			return $thing->paginate ( 50 );
		} else {
			return $thing->get ();
		}
	}
}
