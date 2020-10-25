<?php

defined('THINK_PATH') or exit();
return array(
    /* 其他设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'pay', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'root', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'think_', // 数据库表前缀

    /* 支付设置 */
    'payment' => array(
        'tenpay' => array(
            // 加密key，开通财付通账户后给予
            'key' => 'e82573dc7e6136ba414f2e2affbe39fa',
            // 合作者ID，财付通有该配置，开通财付通账户后给予
            'partner' => '1900000113'
        ),
        'alipay' => array(
            // 收款账号邮箱
            'email' => 'chenf003@yahoo.cn',
            // 加密key，开通支付宝账户后给予
            'key' => 'aaa',
            // 合作者ID，支付宝有该配置，开通易宝账户后给予
            'partner' => '2088101000137799'
        ),
        'aliwappay' => array(
            // 收款账号邮箱
            'email' => 'chenf003@yahoo.cn',
            // 加密key，开通支付宝账户后给予
            'key' => 'aaa',
            // 合作者ID，支付宝有该配置，开通易宝账户后给予
            'partner' => '2088101000137799'
        ),
        'palpay' => array(
            'business' => 'zyj@qq.com'
        ),
        'yeepay' => array(
            'key' => '69cl522AV6q613Ii4W6u8K6XuW8vM1N6bFgyv769220IuYe9u37N4y7rI4Pl',
            'partner' => '10001126856'
        ),
        'kuaiqian' => array(
            'key' => '1234567897654321',
            'partner' => '1000300079901'
        ),
        'unionpay' => array(
            'key' => '88888888',
            'partner' => '105550149170027'
        )
    )
);
