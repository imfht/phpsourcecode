<?php

namespace App\Http\Middleware;

use Closure;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$permission)
    {
        $user=$request->user();
        if($user)
        {
            if($user->can($permission) || (\Config::get('app.debug') && $user->id===1))
            {
                return $next($request);
            }
        }

        return redirect('admin/no_permission');
    }
}
