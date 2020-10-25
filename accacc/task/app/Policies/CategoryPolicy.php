<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Goal $goal        	
	 * @return bool
	 */
	public function destroy(User $user, Category $category) {
		return $user->id === $category->user_id;
	}
}
