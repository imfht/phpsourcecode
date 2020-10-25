<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-02-29 16:57:27
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-13 08:46:48
 */

return [
    'name' => '店滴',
    'version' => '1.0.7',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@vue' => '@common/widgets/adminlte/yii-vue',
        '@npm' => '@vendor/npm-asset',
        '@TencentYoutuyun' => '@vendor/youtu/TencentYoutuyun',
        '@diandi' => '@vendor/yii-diandi',
    ],
    'vendorPath' => dirname(dirname(__DIR__)).'/vendor',
    'modules' => [
        'settings' => [
            'class' => 'yii2mod\settings\Module',
        ],
    ],
    'bootstrap' => [
        // 初始化模块依赖的扩展
        'diandi\addons\loader',
        'queue',
    ],
    'components' => [
        /* ------ 微信业务组件 ------ **/
        'wechat' => [
            'class' => 'common\components\wechat\Wechat',
            'userOptions' => [],  // 用户身份类参数
            'sessionParam' => 'wechatUser', // 微信用户信息将存储在会话在这个密钥
            'returnUrlParam' => '_wechatReturnUrl', // returnUrl 存储在会话中
            'rebinds' => [ // 自定义服务模块
                // 'cache' => 'common\components\Cache',
            ],
        ],
        // 缓存组件
        'cachehelper' => [
            'class' => 'common\helpers\CacheHelper',
        ],
        /* ------ 队列设置 ------ **/
        'queue' => [
            'class' => 'yii\queue\redis\Queue',
            'redis' => 'redis', // 连接组件或它的配置
            'channel' => 'queue', // Queue channel key
            'as log' => 'yii\queue\LogBehavior', // 日志
        ],
        'settings' => [
            'class' => 'yii2mod\settings\components\Settings',
            'modelClass' => 'common\models\Setting',
        ],
        'helper' => [
            'class' => 'common\components\helpers\helper',
        ],
        'service' => [
            'class' => 'common\services\BaseService',
        ],
        'i18n' => [
            'translations' => [
                'yii2mod.settings' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
                'yii2-admin' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@diandi/admin/messages',
                ],
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            //'flushInterval' => 1,
            'targets' => [
               [
                  'class' => 'yii\log\EmailTarget', //默认邮件处理类
                //   'class' => 'api\components\EmailTargetKen',//自定义日志处理类
                  //'levels' => ['error', 'warning', 'trace', 'info'],//各种等级区分，根据需要使用
                  'levels' => ['error', 'warning', 'trace', 'info'],
                //   'categories' => ['email_log'],
                  'mailer' => 'mailer',
                  'message' => [
                     'from' => ['ai@tuhuokeji.com' => 'admin'],
                     'to' => ['ai@tuhuokeji.com'],
                     'subject' => 'Log message',
                  ],
                  'except' => ['yii\web\HttpException:404'],  // 排除404，不然的话你会发现你的邮箱里全塞满了这些邮件
                  'exportInterval' => 1, //阀值一个错误的时候就执行输出
                  'logVars' => [],
               ],
               [
                  'class' => 'yii\log\FileTarget', //默认文件处理类
                  'levels' => ['error', 'warning'],
                  'exportInterval' => 1,
                  'categories' => ['myinfo'],
                  //'categories' => ['yii\*'],//$categories the message categories to filter by. If empty, it means all categories are allowed.
                  'logVars' => ['*'], //记录最基本的 []赋值也可以
                  //'logFile' => '@runtime/logs/order.log'.date('Ymd'),//用日期方式记录日志
               ],
            ],
        ],
    ],
];
