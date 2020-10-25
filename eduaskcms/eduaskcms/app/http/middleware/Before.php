<?php

namespace app\http\middleware;

class Before
{
    public function handle($request, \Closure $next)
    {
        //你的代码
        return $next($request);
    }
}
