<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Monolog\Processor\UidProcessor;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		// $monolog = Log::getMonolog();
		// $monolog->pushProcessor(new UidProcessor());
		\DB::listen ( function ($query) {
			\Log::info ( $query->sql );
		} );
		
		//
		// LengthAwarePaginator::presenter(function (Paginator $paginator) {
		// return new BootstrapFourPresenter($paginator);
		// });
		
		// // Change the ->simplePaginate() presenter
		// Paginator::presenter(function (PaginatorContract $paginator) {
		// return new BootstrapFourPresenter($paginator);
		// });
	}
	
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
