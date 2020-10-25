<?php

declare(strict_types=1);

namespace TencentAI\Kernel;

use TencentAI\TencentAI;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * 是否延时加载提供器。
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * 在容器中注册绑定。
     */
    public function register(): void
    {
        $configPath = __DIR__.'/../../../config/tencent-ai.php';

        $this->mergeConfigFrom($configPath, 'tencent-ai');

        // $this->loadRoutesFrom(__DIR__.'/routes.php');
        // $this->loadMigrationsFrom(__DIR__.'/path/to/migrations');
        // $this->loadTranslationsFrom(__DIR__.'/path/to/translations', 'courier');

        $this->app->singleton(TencentAI::class, function () {
            $app_name = config('tencent-ai.default', 'default');

            return TencentAI::getInstance(
                config('tencent-ai.app.'.$app_name.'.app_id'),
                config('tencent-ai.app.'.$app_name.'.app_key'),
                config('tencent-ai.app.'.$app_name.'.json_format', false),
                config('tencent-ai.app.'.$app_name.'.timeout', 100)
            );
        });

        $this->app->alias('tencent-ai', TencentAI::class);

        //        $this->app->bind(TencentAI::class, function () {
        //            return Application::getInstance(
        //                config('tencent-ai.appID'),
        //                config('tencent-ai.appKey'),
        //                config('tencent-ai.jsonFormat'),
        //                config('tencent-ai.timeout')
        //            );
        //        });
    }

    /**
     * 在注册后进行服务的启动。
     */
    public function boot(): void
    {
        $configPath = __DIR__.'/../../../config/tencent-ai.php';

        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        // $this->loadTranslationsFrom(__DIR__.'/path/to/translations', 'courier');

        // $this->publishes([
        //   __DIR__.'/path/to/translations' => resource_path('lang/vendor/courier'),
        // ]);

        // $this->loadViewsFrom(__DIR__.'/path/to/views', 'courier');

        // $this->publishes([
        //    __DIR__.'/path/to/views' => resource_path('views/vendor/courier'),
        // ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \TencentAI\Console\OCRCommand::class,
            ]);
        }
        // $this->publishes([
        //     __DIR__.'/path/to/assets' => public_path('vendor/courier'),
        // ], 'public');
    }

    /**
     * Get the config path.
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('tencent-ai.php');
    }

    /**
     * 获取提供器提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return ['tencent-ai'];
    }
}
