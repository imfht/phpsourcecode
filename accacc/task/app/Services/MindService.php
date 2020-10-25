<?php

namespace App\Services;

use App\Models\User;
use App\Models\Mind;

/**
 * 思维导图业务逻辑
 *
 * @author edison.an
 *        
 */
class MindService {
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user) {
		return Mind::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'desc' )->get ();
	}
	
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forStatus($status) {
		return Mind::where ( 'status', $status )->orderBy ( 'created_at', 'desc' )->get ();
	}
	
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByStatus(User $user, $status, $is_root, $needPage = false) {
		$note = Mind::where ( 'status', $status )->where ( 'is_root', $is_root )->where ( 'user_id', $user->id )->orderBy ( 'created_at', 'desc' );
		
		if ($needPage) {
			return $note->paginate ( 50 );
		} else {
			return $note->get ();
		}
	}
}
