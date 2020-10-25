<?php

return [
    /**
     * 支付宝
     */
    'alipay' => [
        'appid' => env('PAYMENT_ALIPAY_APPID'),//APPID
        'private_key' => env('PAYMENT_ALIPAY_PRIVATE_KEY', ''),//私钥
        'public_key' => env('PAYMENT_ALIPAY_PUBLIC_KEY', ''),//公钥
    ],
];
