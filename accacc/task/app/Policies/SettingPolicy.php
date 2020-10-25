<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Mind $mind        	
	 * @return bool
	 */
	public function destroy(User $user, Setting $setting) {
		return $user->id === $setting->user_id;
	}
}
