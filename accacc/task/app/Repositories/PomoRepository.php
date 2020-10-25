<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Pomo;

class PomoRepository {
	/**
	 * Get all of the pomos for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user, $needPage = false) {
		$pomo = Pomo::where ( 'user_id', $user->id )->orderBy ( 'updated_at', 'desc' );
		if ($needPage) {
			return $pomo->paginate ( 50 );
		} else {
			return $pomo->get ();
		}
	}
	public function forUserByStatus(User $user, $status, $needPage = false) {
		$pomo = Pomo::where ( 'user_id', $user->id )->where ( 'status', $status )->orderBy ( 'updated_at', 'desc' );
		
		if ($needPage) {
			return $pomo->paginate ( 50 );
		} else {
			return $pomo->get ();
		}
	}
	public function forUserActivePomo(User $user) {
		return Pomo::where ( 'user_id', $user->id )->where ( 'status', 1 )->first ();
	}
	
	/**
	 * Get all of the pomos for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByTime(User $user, $time) {
		return Pomo::where ( 'user_id', $user->id )->where ( 'status', 2 )->where ( 'created_at', '>', $time )->orderBy ( 'created_at', 'desc' )->get ();
	}
	public function create($attr) {
		return Pomo::create ( $attr );
	}
}
