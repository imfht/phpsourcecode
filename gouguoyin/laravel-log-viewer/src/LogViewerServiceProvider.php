<?php

namespace Gouguoyin\LogViewer;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class LogViewerServiceProvider extends ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config';
    const ROUTE_PATH  = __DIR__ . '/../routes';
    const STUB_PATH   = __DIR__ . '/../stubs';
    const VIEW_PATH   = __DIR__ . '/../resources/views';
    const LANG_PATH   = __DIR__ . '/../resources/lang';

    protected $packageName;

    /**
     * Bootstrap the application events.
     * @param LogViewerService $logViewerService
     */
    public function boot(LogViewerService $logViewerService)
    {
        $this->packageName = $logViewerService->getPackageName();

        $this->gate();

        /**
         * 加载路由文件
         */
        $this->loadRoutesFrom(self::ROUTE_PATH . '/web.php');

        /**
         * 指定视图路径
         */
        $this->loadViewsFrom(self::VIEW_PATH, $this->packageName);

        /**
         * 指定语言路径
         */
        $this->loadTranslationsFrom(self::LANG_PATH, $this->packageName);

        /**
         * 发布配置文件
         */
        $this->publishes([
            self::CONFIG_PATH => config_path(),
        ], 'log-viewer-config');

        /**
         * 发布视图目录
         */
        $this->publishes([
            self::VIEW_PATH => resource_path('views/vendor/' . $this->packageName),
        ], 'log-viewer-views');

        /**
         * 发布翻译文件
         */
        $this->publishes([
            self::LANG_PATH => resource_path('lang'),
        ], 'log-viewer-lang');

        /**
         * 发布服务提供者
         */
        $this->publishes([
            self::STUB_PATH . '/LogViewerServiceProvider.stub' => app_path('Providers/LogViewerServiceProvider.php'),
        ], 'log-viewer-provider');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /**
         * 合并配置信息
         */
        $this->mergeConfigFrom(
            self::CONFIG_PATH . '/log-viewer.php', 'log-viewer'
        );
    }

    protected function gate()
    {
        Gate::define($this->packageName, function ($user) {
            return in_array($user->email, [

            ]);
        });
    }
}
