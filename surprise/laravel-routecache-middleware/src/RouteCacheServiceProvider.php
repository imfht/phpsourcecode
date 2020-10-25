<?php
/**
 * RouteCacheServiceProvider.php
 *
 * @author: Cyw
 * @email: chenyunwen01@bianfeng.com
 * @created: 2015/11/12 20:13
 * @logs:
 *
 */
namespace Rose1988c\RouteCache;

use Illuminate\Support\ServiceProvider;

class RouteCacheServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/config/routecache.php' => config_path('routecache.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //$this->mergeConfig();
    }

    /**
     * Merges user's and routecache's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'routecache'
        );
    }
}
