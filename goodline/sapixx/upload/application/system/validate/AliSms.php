<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户验证
 */
namespace app\system\validate;
use think\Validate;

class AliSms extends Validate{

    protected $rule = [
        'phone_id'       => 'require|mobile',
        'sms_code'       => 'require|min:4|max:6',
    ];
    
    protected $message = [
        'phone_id'       => '请输入正确的手机号',
        'sms_code'       => '验证码输出错误',
    ];

    protected $scene = [
        'getsms'   => ['phone_id'],
    ];
}