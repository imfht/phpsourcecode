<?php

namespace app\http\middleware;

class After
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        //你的代码
        return $response;
    }
}
