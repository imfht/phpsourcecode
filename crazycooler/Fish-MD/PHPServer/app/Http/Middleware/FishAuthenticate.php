<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Routing\Router;


class FishAuthenticate
{
    /**
     * Router instance.
     *
     * @var \Dingo\Api\Routing\Router
     */
    protected $router;

    /**
     * Authenticator instance.
     *
     * @var \Dingo\Api\Auth\Auth
     */
    protected $auth;

    /**
     * Create a new auth middleware instance.
     *
     * @param \Dingo\Api\Routing\Router $router
     * @param \Dingo\Api\Auth\Auth      $auth
     *
     * @return void
     */
    public function __construct(Router $router, Auth $auth)
    {
        $this->router = $router;
        $this->auth = $auth;
    }

    /**
     * Perform authentication before a request is executed.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $this->router->getCurrentRoute();

        $fish = $this->auth->user(false);
        if (is_null($fish)) {
            $fish = $this->auth->authenticate($route->getAuthenticationProviders());
        }

        if($fish['privilege'] < 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
