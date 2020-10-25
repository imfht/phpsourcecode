<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Facades\Log;

/**
 * Class LogUserCreated
 *
 * @package App\Listeners
 */
class LogUserCreated {
	
	/**
	 * LogUserCreated constructor.
	 */
	public function __construct() {
		//
	}
	
	/**
	 *
	 * @param UserCreated $event        	
	 */
	public function handle(UserCreated $event) {
		// Access the podcast using $event->podcast...
		$name = $event->user->name;
		$email = $event->user->email;
		Log::info ( 'User Successfully Created: ' . $name . ' ' . $email );
	}
}