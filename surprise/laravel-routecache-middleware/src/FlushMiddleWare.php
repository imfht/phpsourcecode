<?php
/**
 * FlushMiddleWare.php
 *
 * @author: Cyw
 * @email: chenyunwen01@bianfeng.com
 * @created: 2015/11/13 14:47
 * @logs:
 *
 */
namespace Rose1988c\RouteCache;

use Closure;
use Illuminate\Support\Facades\Response;

class FlushMiddleWare extends BaseCacheMiddleware
{

    /**
     * handle
     *
     * @param $request
     * @param Closure $next
     * @param bool|false $time
     * @return Response
     */
    public function handle($request, Closure $next, $ref = false)
    {
        if ($ref == 'ref') {
            // flush ref
            $key = $this->keygen($request->header('Referer'));
        } else if ($ref == 'url') {
            // flush appoint url
            $key = $this->keygen($request->query('flushurl'));
        } else {
            // flush current url
            $key = $this->keygen($request->url());
        }

        //check if key is in cache
        if ($this->has($key)) {

            $this->forget($key);
        }

        return $next($request);
    }
}
