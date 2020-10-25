<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccessLog {
	
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @param \Closure $next        	
	 * @param string|null $guard        	
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null) {
		if (! app ()->runningInConsole ()) {
			if (Auth::guard ( $guard )->guest ()) {
				Log::info ( "ACCESS:" . $_SERVER ['REQUEST_URI'] . http_build_query ( $request->all () ) . '|IP:' . $_SERVER ['REMOTE_ADDR'] );
			} else {
				Log::info ( "ACCESS:" . $_SERVER ['REQUEST_URI'] . http_build_query ( $request->all () ) . '|IP:' . $_SERVER ['REMOTE_ADDR'] . '|USERID:' . $request->user ()->id );
			}
		}
		
		return $next ( $request );
	}
}
