<?php

namespace App\Http\Middleware;

use Closure;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (\Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(route('admin.login'));
            }
        }

        /**
         * 获取当前登录用户
         */
        $user = \Auth::guard($guard)->user();

        /**
         * 超级管理员组成员具有所有权限, 不是超级管理员组的成员需要判断是否具备当前的执行权限
         */
        if (!$user->hasRole('super_admin') && !$user->canPermission(\Route::currentRouteName())) {
            abort(403, '您没有权限访问当前资源');
        }

        return $next($request);
    }
}
