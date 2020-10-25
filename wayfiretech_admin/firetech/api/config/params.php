<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-09 14:07:44
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-18 18:21:19
 */

return [
    'adminEmail' => 'admin@example.com',
    /* ------ token相关 ------ **/
    // token有效期是否验证 默认不验证
    'user.accessTokenValidity' => true,
    // token有效期 默认 2 小时
    'user.accessTokenExpire' => 2 * 60 * 60,
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
    'cache' => [
        'duration' => 20 * 10, //全局缓存时间
    ],
    'api' => [
        'rateLimit' =>300, //速率限制,
        'timeLimit' =>60
    ],
];
