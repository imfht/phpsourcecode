<?php

namespace App\Http\Controllers\Api;

use App\Models\Sms;
use App\Traits\CacheTrait;
use App\Validates\SmsValidate;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;

class ThirdController extends ApiController
{
    use CacheTrait;

    public function sendSms(Request $request, SmsValidate $validate, EasySms $easySms)
    {
        $request_data = $request->all();
        $rest_validate = $validate->storeValidate($request_data);
        if ($rest_validate['status'] === true) {
            $phone = $request_data['phone'];
            $code = $rest_validate['data']['sms_code'];
            $expiredAt = $rest_validate['data']['expired_at'];
            $created_at = $rest_validate['data']['created_at'];
            try {
                /*
                $easySms->send($phone, [
                    'content' => '【xxxx】您的验证码是' . $code . ' ，切勿泄露给他人。'
                ]);
                */
                $easySms->send($phone, [
                    'conent' => '您的验证码为: 6379',
                    'template' => 'SMS_126295008',
                    'data' => [
                        'code' => $code
                    ]
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('yunpian')->getMessage();
                return $this->failed($message ?? '短信发送异常');
            }
            Sms::create([
                'phone' => $phone,
                'type' => 'O',
                'code' => $code,
                'ip' => get_client_ip()
            ]);
            $key = $this->cachePut('smsCode', $phone, ['phone' => $phone, 'sms_code' => $code, 'created_at' => $created_at], $expiredAt);
            return $this->success(['key' => $key, 'expired_at' => $expiredAt * 60], $rest_validate['message']);
        } else {
            return $this->failed($rest_validate['message']);
        }
    }

    public function checkSmsCode(Request $request)
    {
        $verifyData = $this->cacheGet($request->sms_key);

        if (!$verifyData) {
            return $this->failed('验证码已失效', 422);
        }
        if (!hash_equals(strval($verifyData['sms_code']), $request->sms_code)) return $this->failed('验证码不正确');
        return $this->success('验证通过');
    }
}
