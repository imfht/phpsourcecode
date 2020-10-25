<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //判断是否是https
        if (strpos(config('app.url'), 'https') !== false) {
            URL::forceScheme('https');
        }
        //验证金额
        Validator::extend('price', function ($attribute, $value, $parameters) {
            return preg_match("/(^[-]?[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[-]?[0-9]\.[0-9]([0-9])?$)/", $value);
        });
        //验证手机
        Validator::extend('mobile', function ($attribute, $value, $parameters) {
            return preg_match("/^1[3456789]{1}\d{9}$/", $value);
        });
    }
}
