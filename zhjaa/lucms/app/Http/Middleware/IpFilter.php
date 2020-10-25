<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use DB;

class IpFilter
{
    const CACHE_KEY = 'ip_filter_list';
    const CACHE_MINUTES = 10;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $client_ip = get_client_ip();
        if (!Cache::has(self::CACHE_KEY)) {
            $white_ip_list = DB::table('ip_filters')->where('type', 'white')->pluck('ip')->toArray();
            $black_ip_list = DB::table('ip_filters')->where('type', 'black')->pluck('ip')->toArray();
            Cache::put(self::CACHE_KEY, [$white_ip_list, $black_ip_list], self::CACHE_MINUTES);
        } else {
            list($white_ip_list, $black_ip_list) = Cache::get(self::CACHE_KEY);
        }
        if ($white_ip_list) {
            if (!in_array($client_ip, $white_ip_list)) {
                abort(401, '没有访问权限');
            }
        }
        if ($black_ip_list) {
            if (in_array($client_ip, $white_ip_list)) {
                abort(401, '没有访问权限');
            }
        }
        return $next($request);
    }
}
