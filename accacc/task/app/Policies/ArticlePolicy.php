<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Goal $goal        	
	 * @return bool
	 */
	public function destroy(User $user, Article $article) {
		return $user->id === $article->user_id;
	}
}
