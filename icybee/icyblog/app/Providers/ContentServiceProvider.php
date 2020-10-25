<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use  App\Services\blog\BlogContentService;

class ContentServiceProvider extends ServiceProvider {

	protected $defer = true;
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
		$this->app->bind('BlogContentService',function(){
			return new BlogContentService;
		});
	}
	

}
