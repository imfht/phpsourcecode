<?php

return [
    'interval_time' => '60',//验证码发送间隔时间
    'out_time' => '300',//短信有效时间
    /**
     * 短信配置
     */
    'chuanglan' => [
        'api_account' => env('CHUANGLAN_API_ACCOUNT'),
        'api_password' => env('CHUANGLAN_API_PASSWORD'),
        'url' => 'http://smssh1.253.com/msg/send/json'
    ],
];
