<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-09 01:37:15
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-14 01:37:10
 */



$config = [
    'components' => [
        'fans' => [
            'class' => 'app\modules\wechat\components\Fans'
        ]
    ],
    'params' => [
        // token有效期是否验证 默认不验证
        'user.accessTokenValidity' => true,
        // token有效期 默认 2 小时
        'user.accessTokenExpire' => 2 * 60 * 60,
        // 'user.accessTokenExpire' => 60, //1分钟
        // refresh token有效期是否验证 默认开启验证
        'user.refreshTokenValidity' => true,
        // refresh token有效期 默认30天
        'user.refreshTokenExpire' => 30 * 24 * 60 * 60,
        // 签名验证默认关闭验证，如果开启需了解签名生成及验证
        'user.httpSignValidity' => false,
        // 签名授权公钥秘钥
        'user.httpSignAccount' => [
            'doormen' => 'e3de3825cfbf',
        ],
        // 微信配置 具体可参考EasyWechat 
        'wechatConfig' => [],

        // 微信支付配置 具体可参考EasyWechat
        // 'wechatPaymentConfig' => [
        //     // 必要配置
        //     'app_id'             => '',
        //     'mch_id'             => '',
        //     'key'                => '',   // API 密钥
        //     // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        //     // 'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
        //     // 'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
        //     'notify_url'         => '',     // 你也可以在下单时单独设置来想覆盖它
        // ],

        // 微信小程序配置 具体可参考EasyWechat
        'wechatMiniProgramConfig' => [
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => __DIR__ . '/log/wechat.log',
            ],
            'cache' => [
                'class' => 'yii\redis\Cache',
                'redis' => [
                    'hostname' => '127.0.0.1',
                    'port' => 6379,
                    'database' => 0,
                ]
            ]
        ],

        // 微信开放平台第三方平台配置 具体可参考EasyWechat
        'wechatOpenPlatformConfig' => [],

        // 微信企业微信配置 具体可参考EasyWechat
        'wechatWorkConfig' => [],

        // 微信企业微信开放平台 具体可参考EasyWechat
        'wechatOpenWorkConfig' => [],
    ]

];

return $config;
