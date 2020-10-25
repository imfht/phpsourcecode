<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-05 08:21:08
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-09 12:15:52
 */
$params = array_merge(
    require __DIR__.'/../../common/config/params.php',
    require __DIR__.'/../../common/config/params-local.php',
    require __DIR__.'/params.php',
    require __DIR__.'/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'diandi\admin\Module',
            'mainLayout' => '@app/views/layouts/main.php', //自己的layout
        ],
        'addons' => [
            'class' => 'diandi\addons\Module',
        ],
    ],
    'aliases' => [
        '@diandi/admin' => '@vendor/yii-diandi/yii2-admin',
        '@diandi/adminlte' => '@vendor/yii-diandi/adminlte/src',
        '@diandi/addons' => '@vendor/yii-diandi/yii2-addons',
        '@addonstpl' => '@frontend/web/backend/giitpl/addons',
    ],
    'as access' => [
        'class' => 'diandi\admin\components\AccessControl',
        'allowActions' => [
            'site/*', //允许访问的节点，可自行添加
            // 'gii/*', //允许访问的节点，可自行添加
            // 'admin/*', //允许所有人访问admin节点及其子节点
            'system/welcome/index',
            'system/settings/set-cache',
            'addons/addons/index',
            'upload/upload',
            'module',
        ],
    ],
    'components' => [
        //   'response' => [
        //     'class' => 'yii\web\Response',
        //     'on beforeSend' => function ($event) {
        //         $response = $event->sender;
        //         if ($response->data !== null) {
        //             $response->data = [
        //                 'success' => $response->isSuccessful,
        //                 'data' => $response->data,
        //             ];
        //             $response->statusCode = 200;
        //         }
        //     },
        // ],
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
        'request' => [
            'csrfParam' => '_csrf-backend',
            // 'as requestmethod' => common\behaviors\HttpRequstMethod::class,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'class' => 'yii\web\View',
            'renderers' => [
                'vue' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    'cachePath' => '@runtime/Smarty/cache',
                    'options' => [
                        'left_delimiter' => '<{',
                        'right_delimiter' => '}>',
                    ],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'diandi\\admin\\components\\DbManager', // 使用数据库管理配置文件
            'defaultRoles' => array('基础权限组'), //默认角色
        ],
        'urlManager' => [
            //用于表明urlManager是否启用URL美化功能，在Yii1.1中称为path格式URL，

            // Yii2.0中改称美化。

            // 默认不启用。但实际使用中，特别是产品环境，一般都会启用。

            'enablePrettyUrl' => true,

            // 是否启用严格解析，如启用严格解析，要求当前请求应至少匹配1个路由规则，

            // 否则认为是无效路由。

            // 这个选项仅在 enablePrettyUrl 启用后才有效。

            'enableStrictParsing' => false,

            // 是否在URL中显示入口脚本。是对美化功能的进一步补充。

            'showScriptName' => false,

            // 指定续接在URL后面的一个后缀，如 .html 之类的。仅在 enablePrettyUrl 启用时有效。

            'suffix' => '',

            'rules' => [
                "<controllers:\w+>/<id:\d+>" => '<controllers>/view',
                "<controllers:\w+>/<action:\w+>" => '<controllers>/<action>',
            ],
            'cache' => 'cache',
        ],
        'assetManager' => [
            'linkAssets' => true,
          ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:Y-m-d',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
            'timeFormat' => 'php:H:i:s',
        ],
    ],
    'params' => $params,
];
