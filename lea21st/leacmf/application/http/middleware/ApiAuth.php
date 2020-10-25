<?php

namespace app\http\middleware;

use app\common\library\Auth;
use Firebase\JWT\JWT;
use think\Container;
use think\exception\HttpException;

class ApiAuth
{
    public function handle($request, \Closure $next)
    {
        //验证登录
        $auth = Auth::instance();
        $jwt  = substr($request->header('Authorization'), 7);

        $user = null;
        try {
            $jwt = (array)JWT::decode($jwt, env('APP_SECRET'), ['HS256']);
            if ($jwt && $jwt['exp'] > time()) {
                $user = $auth->user($jwt);
            }
        } catch (\Exception $e) {
            $jwt = null;
        }

        //注入用户
        app()->auth = $auth;
        app()->user = $user;

        //检查访问权限
        if (!$auth->checkPublicUrl()) {
            if (empty($jwt)) {
                throw new HttpException(401, '未授权访问');
            }

            if (!$user) {
                throw new HttpException(401, '登录已过期，请重新登录');
            }
            if ($user['token'] != $jwt['token']) {
                throw new HttpException(401, '用户验证失败，请重新登录');
            }
        }

        return $next($request);
    }
}
