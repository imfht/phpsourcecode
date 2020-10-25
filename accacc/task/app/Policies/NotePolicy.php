<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Note;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy {
	use HandlesAuthorization;
	
	/**
	 * Determine if the given user can delete the given task.
	 *
	 * @param User $user        	
	 * @param Task $task        	
	 * @return bool
	 */
	public function destroy(User $user, Note $note) {
		return $user->id === $note->user_id;
	}
}
