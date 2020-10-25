<?php

namespace App\Providers;

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
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->register(\Maatwebsite\Excel\ExcelServiceProvider::class);

        if (!class_exists('Excel')) {
            class_alias('Maatwebsite\Excel\Facades\Excel', 'Excel');
        }
    }
}
