<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        //默认web模块路由
        Route::namespace('App\Http\Controllers')->group(base_path('routes/web.php'));

        //main模块的路由
        Route::prefix('main')->namespace('App\Http\Controllers\Main')->group(base_path('routes/main.php'));

        //builder模块的路由
        if(app()->environment() == 'local'){
            Route::prefix('builder')->namespace('Tools\Builder') ->group(base_path('tools/Builder/router.php'));
        }
    }
}
