<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 2:17 PM
 */

namespace App\Http\Middleware;

use App\Services\SignService;
use Closure;

class SignCheck
{

    /**
     * 验证签名
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws \App\Exceptions\ApiException
     */
    public function handle($request, Closure $next)
    {
        $post_data = $request->post();
        //$this->checkSign($post_data);
        return $next($request);
    }

    /**
     * 验证签名
     * @param $post_data
     * @throws \App\Exceptions\ApiException
     */
    public function checkSign($post_data)
    {
        if (!$post_data) {
            api_error(__('api.missing_params'));
        }
        if (!isset($post_data['timestamp']) || !$post_data['timestamp']) {
            api_error(__('api.timestamp_error'));
        }
        if (time() - $post_data['timestamp'] > 20) {
            api_error(__('api.timestamp_out'));
        }
        $sign_service = new SignService();
        //除去待签名参数数组中的空值和签名参数
        $filter_data = $sign_service->arrayFilter($post_data);
        //生成签名结果
        $sign = $sign_service->buildSign($filter_data);
        if (!isset($post_data['sign']) || $sign != $post_data['sign']) {
            api_error(__('api.invalid_sign'));
        }
    }
}
