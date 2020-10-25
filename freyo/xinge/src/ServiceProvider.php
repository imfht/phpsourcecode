<?php

namespace Freyo\Xinge;

use Freyo\Xinge\Client\XingeApp;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config.php', 'services.xinge'
        );

        $this->app->singleton('xinge.android', function ($app) {
            return new Client(
                new XingeApp(
                    $app['config']['services.xinge.android.access_id'],
                    $app['config']['services.xinge.android.secret_key']
                )
            );
        });

        $this->app->singleton('xinge.ios', function ($app) {
            return new Client(
                new XingeApp(
                    $app['config']['services.xinge.ios.access_id'],
                    $app['config']['services.xinge.ios.secret_key']
                )
            );
        });

        $this->app->when(AndroidChannel::class)
                  ->needs(Client::class)
                  ->give('xinge.android');

        $this->app->when(iOSChannel::class)
                  ->needs(Client::class)
                  ->give('xinge.ios');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['xinge.android', 'xinge.ios'];
    }
}
