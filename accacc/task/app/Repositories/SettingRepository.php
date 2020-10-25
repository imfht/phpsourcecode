<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Setting;

class SettingRepository {
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user) {
		return Setting::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'desc' )->first ();
	}
	
	/**
	 * Get Setting By cal_token
	 *
	 * @param string $cal_token        	
	 */
	public function forCalToken(string $cal_token) {
		return Setting::where ( 'cal_token', $cal_token )->first ();
	}
	
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forStatus($status) {
		return Setting::where ( 'status', $status )->orderBy ( 'created_at', 'desc' )->get ();
	}
	public function getStartList() {
		return Setting::where ( 'is_start_kindle', 1 )->get ();
	}
}
