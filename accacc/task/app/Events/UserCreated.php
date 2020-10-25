<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserCreated
 *
 * @package App\Events
 */
class UserCreated extends Event {
	use SerializesModels;
	/**
	 *
	 * @var User
	 */
	public $user;
	/**
	 * Create a new event instance.
	 *
	 * @param User $user        	
	 */
	public function __construct(User $user) {
		$this->user = $user;
	}
}