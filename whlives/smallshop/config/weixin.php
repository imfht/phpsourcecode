<?php

return [
    'app' => [
        'appid' => env('WX_APP_APP_ID'),
        'secret' => env('WX_APP_SECRET'),
        'mch_id' => env('WX_APP_MCH_ID'),
        'api_key' => env('WX_APP_KEY'),
        'sslcert_path' => env('WX_APP_SSLCERT_PATH'),
        'sslkey_path' => env('WX_APP_SSLKEY_PATH')
    ],
    //小程序
    'miniprogram' => [
        'appid' => env('WX_MINIPROGRAM_APP_ID'),
        'secret' => env('WX_MINIPROGRAM_SECRET'),
        'mch_id' => env('WX_MINIPROGRAM_MCH_ID'),
        'api_key' => env('WX_MINIPROGRAM_KEY'),
        'sslcert_path' => env('WX_MINIPROGRAM_SSLCERT_PATH'),
        'sslkey_path' => env('WX_MINIPROGRAM_SSLKEY_PATH')
    ],
    //公众号
    'mp' => [
        'appid' => env('WX_MP_APP_ID'),
        'secret' => env('WX_MP_SECRET'),
        'mch_id' => env('WX_MP_MCH_ID'),
        'api_key' => env('WX_MP_KEY'),
        'sslcert_path' => env('WX_MP_SSLCERT_PATH'),
        'sslkey_path' => env('WX_MP_SSLKEY_PATH')
    ]
];
