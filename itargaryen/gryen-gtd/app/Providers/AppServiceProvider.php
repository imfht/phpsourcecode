<?php

namespace App\Providers;

use App\Config;
use App\ConfigMany;
use Exception;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        try {
            view()->share('CONFIG', Config::getAllConfig());
            view()->share('CONFIG_MANY', ConfigMany::getAllConfig());
        } catch (Exception $exception) {
            logger('AppServiceProvider:boot share CONFIG CONFIG_MANY:Database is not initialized');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
