<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

/**
 * Class LogSuccessfulLogout
 *
 * @package App\Listeners
 */
class LogSuccessfulLogout {
	
	/**
	 * LogSuccessfulLogout constructor.
	 */
	public function __construct() {
	}
	
	/**
	 *
	 * @param Logout $event        	
	 */
	public function handle(Logout $event) {
		// $name = $event->user->name;
		// $email = $event->user->email;
		// Log::info('Logout Successful: '.$name.' '.$email);
	}
}