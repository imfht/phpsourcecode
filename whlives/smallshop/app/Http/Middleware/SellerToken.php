<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 2:17 PM
 */

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;

class SellerToken
{
    public function handle($request, Closure $next)
    {
        $tokenService = new TokenService('seller');
        $token = $tokenService->getToken();
        if ($token) {
            $tokenService->refreshToken();
        } else {
            api_error(__('admin.invalid_token'));
        }
        return $next($request);
    }

}