<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\Admin::observe(\App\Observers\AdminObserver::class);
        \App\Models\Role::observe(\App\Observers\RoleObserver::class);
        \App\Models\Rule::observe(\App\Observers\RuleObserver::class);
        \App\Models\RoleAuth::observe(\App\Observers\RoleAuthObserver::class);
        Carbon::setLocale('zh');
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
