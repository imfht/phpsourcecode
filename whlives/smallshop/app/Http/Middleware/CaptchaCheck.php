<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/1
 * Time: 下午2:58
 */

namespace App\Http\Middleware;

use Closure;

/**
 * api接口验证短信验证码
 * Class CheckSignToken
 * @package App\Http\Middleware
 */
class CaptchaCheck
{
    public function handle($request, Closure $next)
    {
        $code = $request->post('code');
        $mobile = $request->post('mobile');
        if (!$code) {
            api_error(__('api.sms_captcha_error'));
        }
        if (!$mobile) {
            api_error(__('api.missing_params'));
        }
        $redis_key = 'captcha:' . $mobile;
        $captcha = cache($redis_key);
        if (!$captcha) {
            api_error(__('api.sms_captcha_error'));//错误
        }
        if ($captcha['end_at'] < (time() - config('sms.out_time'))) {
            api_error(__('api.sms_captcha_time_out'));//超时
        }
        if ($captcha['mobile'] != $mobile) {
            api_error(__('api.sms_captcha_error'));//手机不匹配
        }
        if ($captcha['code'] != $code) {
            api_error(__('api.sms_captcha_error'));//错误
        }
        if ($captcha['device'] != get_device()) {
            api_error(__('api.sms_captcha_error'));//设备不匹配
        }
        cache([$redis_key => false], 0);//验证通过删除验证码
        return $next($request);
    }
}