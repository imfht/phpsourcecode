<?php


namespace Yuntongxun\Providers;


use Illuminate\Support\ServiceProvider;
use Yuntongxun\YuntongxunSms;

class YuntongxunSmsServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['yuntongxunsms'] = $this->app->share(function ($app) {
            return new YuntongxunSms();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['yuntongxunsms'];
    }
}