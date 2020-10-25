<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;

/**
 * Class LogLockout
 *
 * @package App\Listeners
 */
class LogLockout {
	
	/**
	 * LogLockout constructor.
	 */
	public function __construct() {
	}
	
	/**
	 *
	 * @param Lockout $event        	
	 */
	public function handle(Lockout $event) {
		$email = $event->user->email;
		Log::info ( 'Login Lockout: ' . $email );
	}
}