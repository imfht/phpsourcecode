<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;

/**
 * Class LogMessageSending
 *
 * @package App\Listeners
 */
class LogMessageSending {
	
	/**
	 * LogMessageSending constructor.
	 */
	public function __construct() {
	}
	
	/**
	 *
	 * @param MessageSending $event        	
	 */
	public function handle(MessageSending $event) {
		// TODO Figure out log field
		// $email = $event->user->email;
		Log::info ( 'Message Sent' );
	}
}