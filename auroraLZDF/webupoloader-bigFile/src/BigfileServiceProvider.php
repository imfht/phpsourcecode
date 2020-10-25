<?php

namespace AuroraLZDF\Bigfile;

use Illuminate\Support\ServiceProvider;

class BigfileServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = false; //TODO： 延迟加载服务。启动延迟加载，会导致注册路由不能访问？

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');   // 注册扩展包路由

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/bigfile'),  // 发布视图目录到resources 下
            __DIR__.'/config/bigfile.php' => config_path('bigfile.php'), // 发布配置文件到 laravel 的config 下
            __DIR__.'/public' => base_path('public/vendor/bigfile'),    // 发布静态文件到 public 下
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // 单例绑定服务
        $this->app->singleton('bigfile', function () {
            return $this->app->make('AuroraLZDF\Bigfile\Bigfile');
        });
    }
}
