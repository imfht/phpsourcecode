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

class TokenCheck
{

    /**
     * 验证token
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws \App\Exceptions\ApiException
     */
    public function handle($request, Closure $next)
    {
        $token_service = new TokenService('api');
        $token_data = $token_service->getToken();
        if (!$token_data || !$token_data['id']) {
            api_error(__('api.invalid_token'));
        }
        $this->checkDevice($token_data);
        return $next($request);
    }

    /**
     * 验证设备是否异常
     * @param $token_data
     * @throws \App\Exceptions\ApiException
     */
    public function checkDevice($token_data)
    {
        $device = get_device();
        $platform = get_platform();

        if ($token_data['device'] != $device) {
            api_error(__('api.invalid_device'));
        }
        if ($token_data['platform'] != $platform) {
            api_error(__('api.invalid_platform'));
        }
    }
}