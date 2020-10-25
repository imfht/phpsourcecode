<?php

namespace App\Providers;

use App\Models\Link;
use App\Traits\SystemConfigTrait;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;

class CommonDataServiceProvider extends ServiceProvider
{
    use SystemConfigTrait;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $session_local = Session::get('local');
            if (!$session_local) {
                session(['local' => 'Zh-cn']);
                $session_local = Session::get('local');
            }
            \Illuminate\Support\Facades\App::setLocale($session_local);
            $view->with('session_local', $session_local);

            $config_list = $this->getSystemConfigFunction(['about_us']);
            dd($config_list);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
