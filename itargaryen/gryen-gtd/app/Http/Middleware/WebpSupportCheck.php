<?php

namespace App\Http\Middleware;

use Closure;

class WebpSupportCheck
{
    /**
     * 检测浏览器是否支持 webp.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->webp = preg_match('/image\/webp/', $request->header('Accept'));

        return $next($request);
    }
}
