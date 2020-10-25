<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [ 
			'App\Events\Event' => [ 
					'App\Listeners\EventListener' 
			],
			'Illuminate\Auth\Events\Login' => [ 
					'App\Listeners\LogSuccessfulLogin' 
			],
			'Illuminate\Auth\Events\Logout' => [ 
					'App\Listeners\LogSuccessfulLogout' 
			],
			
			'Illuminate\Auth\Events\Failed' => [ 
					'App\Listeners\LogFailedLogin' 
			],
			'Illuminate\Auth\Events\Lockout' => [ 
					'App\Listeners\LogLockout' 
			],
			'Illuminate\Mail\Events\MessageSending' => [ 
					'App\Listeners\LogMessageSending' 
			],
			'App\Events\UserCreated' => [ 
					'App\Listeners\LogUserCreated' 
			] 
	];
	
	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot() {
		parent::boot ();
		
		//
	}
}
