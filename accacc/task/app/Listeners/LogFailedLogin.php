<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;

/**
 * Class LogFailedLogin
 *
 * @package App\Listeners
 */
class LogFailedLogin {
	
	/**
	 * LogFailedLogin constructor.
	 */
	public function __construct() {
	}
	
	/**
	 *
	 * @param Failed $event        	
	 */
	public function handle(Failed $event) {
		$email = isset ( $event->user->email ) ? $event->user->email : '';
		Log::info ( 'Login Failed: ' . $email );
	}
}