<?php
// +----------------------------------------------------------------------
// | 路易通碎屏保
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace app\index\middleware;


use think\facade\Cookie;
use think\Request;

class AuthMiddleware
{
    public function handle(Request $request, callable $next)
    {
        if ($request->action() === 'login') {
            return $next($request);
        }
        // 验证登录权限
        $token = Cookie::get('loginToken');
        if (empty($token)) {
            // return redirect('/#login');
        }

        return $next($request);
    }

}