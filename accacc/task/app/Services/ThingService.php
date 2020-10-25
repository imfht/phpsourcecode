<?php

namespace App\Services;

use App\Models\User;
use App\Models\Thing;

/**
 * 记事管理业务逻辑
 *
 * @author edison.an
 *        
 */
class ThingService {
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
