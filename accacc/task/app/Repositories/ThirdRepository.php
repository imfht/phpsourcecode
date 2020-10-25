<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Third;

class ThirdRepository {
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user) {
		return Third::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'asc' )->get ();
	}
	public function forUserThirdId(User $user, $third_id, $source = 'fanfou') {
		return Third::where ( 'user_id', $user->id )->where ( 'third_id', $third_id )->where ( 'source', $source )->orderBy ( 'created_at', 'asc' )->first ();
	}
	public function forUserSource(User $user, $source = 'fanfou') {
		return Third::where ( 'user_id', $user->id )->where ( 'source', $source )->orderBy ( 'created_at', 'asc' )->first ();
	}
	public function forThirdId($third_id) {
		return Third::where ( 'third_id', $third_id )->first ();
	}
}
