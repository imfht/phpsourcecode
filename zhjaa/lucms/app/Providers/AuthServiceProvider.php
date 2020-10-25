<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes(function (RouteRegistrar $router) {
            $router->forAccessTokens();
        }, ['prefix' => 'api/oauth']);
//    }, ['prefix' => 'api/oauth', 'middleware' => 'passport-administrators']);

//        Passport::tokensExpireIn(now()->addMinute(1));
//        Passport::refreshTokensExpireIn(now()->addDay(1));
        Passport::tokensExpireIn(now()->addDay(3));
        Passport::refreshTokensExpireIn(now()->addDay(3));

    }
}
