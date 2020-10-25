<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-05 08:35:24
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-06 15:30:20
 */
return [
    'uploadFile' => [
        'extensions' => ['jpg', 'png', 'jpeg', 'jpe', 'pdf', 'mp4'],
        'mime_types' => ['image/*', 'application/pdf', 'video/mp4'],
        'max_size' => 10 * 1024 * 1024,
        'min_size' => 1,
        'message' => '上传失败',
        'pluginOptions' => [
            'uploadUrl' => '/upload/upload/uploadfile',

            'showUpload' => true,
            'uploadExtraData' => [
                'field' => 'DdGoods[video]',
                'path' => 'goods',
            ],
            'maxFileCount' => 1,
        ],
        'theme' => 'fa',
    ],
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'conf' => [],
    'diandiai' => [
        'APP_ID' => '',
        'API_KEY' => '',
        'SECRET_KEY' => '',
    ],
    // 微信配置
    // 微信配置 具体可参考EasyWechat
    'wechatConfig' => [
        'app_id' => '',
        'secret' => '',
        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',
    ],

    // 微信支付配置 具体可参考EasyWechat
    'wechatPaymentConfig' => [],

    // 微信小程序配置 具体可参考EasyWechat
    'wechatMiniProgramConfig' => [],

    // 微信开放平台第三方平台配置 具体可参考EasyWechat
    'wechatOpenPlatformConfig' => [],

    // 微信企业微信配置 具体可参考EasyWechat
    'wechatWorkConfig' => [],

    // 微信企业微信开放平台 具体可参考EasyWechat
    'wechatOpenWorkConfig' => [],
    'cache'=>[
        'duration'=>20*10,//全局缓存时间
    ]
];
