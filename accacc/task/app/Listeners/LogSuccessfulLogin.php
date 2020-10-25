<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

/**
 * Class LogSuccessfulLogin
 *
 * @package App\Listeners
 */
class LogSuccessfulLogin {
	
	/**
	 * LogSuccessfulLogin constructor.
	 */
	public function __construct() {
	}
	
	/**
	 *
	 * @param Login $event        	
	 */
	public function handle(Login $event) {
		$name = $event->user->name;
		$email = $event->user->email;
		Log::info ( 'Login Successful: ' . $name . ' ' . $email );
		
		$user = $event->user;
		$user->last_login = date ( 'Y-m-d H:i:s' );
		$user->save ();
	}
}