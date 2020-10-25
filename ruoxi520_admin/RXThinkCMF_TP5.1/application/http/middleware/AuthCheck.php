<?php

namespace app\http\middleware;

class AuthCheck
{
    /**
     * 中间件执行句柄
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @author 牧羊人
     * @date 2020/1/2
     */
    public function handle($request, \Closure $next)
    {
        // TODO...
        return $next($request);
    }
}
