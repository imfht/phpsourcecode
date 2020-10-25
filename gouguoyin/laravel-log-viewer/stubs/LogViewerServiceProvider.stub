<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Gouguoyin\LogViewer\LogViewerServiceProvider as ServiceProvider;

class LogViewerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the log-viewer gate.
     *
     * This gate determines who can access log-viewer in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define($this->packageName, function ($user) {
            return in_array($user->email, [
                // '245629560@qq.com', 'gouugoyin@qq.com'
            ]);
        });

        /*
        Gate::define($this->packageName, function ($user = null) {
            return true;
        });
        */
    }
}
