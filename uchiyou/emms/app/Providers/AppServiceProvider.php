<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * 防止如下异常
         * SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key len
gth is 767 bytes (SQL: alter table `users` add unique `users_email_unique`(`email`))
         */
    	Schema::defaultStringLength(191);
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
