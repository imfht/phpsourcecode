<?php
/**
 * CacheMiddleWare.php
 *
 * @author: Cyw
 * @email: chenyunwen01@bianfeng.com
 * @created: 2015/11/12 20:15
 * @logs:
 *
 */
namespace Rose1988c\RouteCache;

use Closure;
use Illuminate\Support\Facades\Response;

class CacheMiddleWare extends BaseCacheMiddleware
{

    /**
     * handle
     *
     * @param $request
     * @param Closure $next
     * @param bool|false $time
     * @return Response
     */
    public function handle($request, Closure $next, $time = false)
    {
        // lifetime
        $time = $time ? $time : $this->config['cache_time'];

        //check if cache is enabled
        if ($this->enabled()) {

            //generate e key to search by url
            $key = $this->keygen($request->url());

            //check if key is in cache
            if ($this->has($key)) {
                //fetch and return the key
                return response($this->get($key));
            } else {
                //hold next request
                $response = $next($request);

                //if not stored it by key and return the value
                $this->put($key, $response->getContent(), $time);
                return $response;
            }
        }

        return $next($request);
    }
}
