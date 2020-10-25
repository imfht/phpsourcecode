<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\KindleLog;

class KindleLogRepository {
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user) {
		return KindleLog::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'desc' )->get ();
	}
	
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByStatus(User $user, $status, $is_root, $needPage = false) {
		$note = KindleLog::where ( 'status', $status )->where ( 'user_id', $user->id )->orderBy ( 'created_at', 'desc' );
		
		if ($needPage) {
			return $note->paginate ( 50 );
		} else {
			return $note->get ();
		}
	}
}
