<?php

namespace App\Validates;

use App\Models\Sms;
use App\Traits\RedisTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;

class  SmsValidate extends Validate
{
    use RedisTrait;
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $date = date('Y-m-d H:i');
        $rules = [
            'phone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/',
            ],
            'sign' => [
                'required',
                Rule::in([$date])
            ]
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {


            $rest_can_send_sms = $this->isCouldSendSms($request_data['phone']);
            if ($rest_can_send_sms === true) {
                $now = time();
                $this->data = [
                    'sms_code' => $this->generateCode(6),
                    'created_at' => $now,
                    'end_at' => $now + 5 * 60,
                ];
                return $this->baseSucceed($this->data, $this->message);
            } else {
                return $this->baseFailed($this->message, $this->data);
            }
        } else {
            return $this->baseFailed($this->message);
        }

    }

    public function checkMobileCaptcha($phone, $input_codde)
    {
        $redis_data = $this->getRedis('smsCode', $phone);

        if (time() > $redis_data->end_at) {
            $setting_time = Config::get('set_time.sms_code_expire_time');
            return $this->baseFailed($setting_time['message']);
        }
        if ($redis_data->code != $input_codde) {
            return $this->baseFailed('短信验证码错误');
        }
        return $this->baseSucceed([],$this->message);

    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
            'sign.required' => '签名不能为空',
            'sign.in' => '签名不正确',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            $this->message = $validator->errors()->first();;
            return false;
        }
        return true;
    }

    protected function generateCode($length = 6)
    {
        return rand(pow(10, ($length - 1)), pow(10, $length) - 1);
    }


    protected function isCouldSendSms($phone)
    {
        $sms = Sms::Where('phone', $phone)->orderBy('created_at', 'desc')->first();
        if ($sms) {
            $time = time() - strtotime($sms->created_at);
            $setting_time = Config::get('set_time.sms_code_can_send_after');
            if ($time < $setting_time['time']) {
                $this->message = $setting_time['message'];
                $this->data['nex_operate_time'] = $time;
                return false;
            }
        }

        return true;
    }
}
