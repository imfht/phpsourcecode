<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['comments', 'yee'],

    'language' => 'zh-CN',
    'sourceLanguage' => 'en-US',

    'components' => [
        'yee' => [
            'class' => 'yeesoft\Yee',
        ],
        'settings' => [
            'class' => 'yeesoft\components\Settings'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'yeesoft\components\User',
            'on afterLogin' => function ($event) {
                \yeesoft\models\UserVisitLog::newVisitor($event->identity->id);
            }
        ],

        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => '@yeesoft/yee-theme/dist',
                     'css' => ['css/theme.min.css']
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => '@yeesoft/yee-theme/dist',
                     'js' => ['js/bootstrap.min.js',]
                ],
            ],
        ],

        //add wx config by kzeng
        'wx' => [
            'class' => 'frontend\models\WX',
            'config' => [
            'debug' => true,

            // zhaohuaxishi
            // 'app_id' => 'wx2b13e34e00b17f72',
            // 'secret' => '5ad896a8eaa008fa621a423053d02955',
            // 'token'  => 'weixin',

            // yizhenwenhua
            // 'app_id' => 'wx471f9a870dca05ad',
            // 'secret' => '2c48d484a9b6763275a448e00574df04',
            // 'token'  => 'weixin',

            // bookgo test01 
            // 'app_id' => 'wx0de82fc1fe1ba445',
            // 'secret' => 'e9600bf0b0d731f8b219c863f9855d70',
            // 'token'  => 'bookgoal',
            
            // bookgo test00 
            'app_id' => 'wx3c365019188c298d',
            'secret' => '8dfe8c9a011306d00eda7a14a1dfcafa',
            'token'  => 'bookgoal',

            // behero01
            // 'app_id' => 'wx637aeae4cedb37db',
            // 'secret' => '01e579e1cb2685a1206320740e79def4',
            // 'token'  => 'beesoft',

             //'aes_key' => 'aaa',
             'log' => [
                 'level' => 'debug',
                 'file'  => '../runtime/easywechat.log',
             ],
             'oauth' => [
                 'scopes' => ['snsapi_base'], // scopes: snsapi_userinfo, snsapi_base, snsapi_login
                 //'callback' => '/examples/oauth_callback.php',
             ],
             'payment' => [
                 'merchant_id' => '',
                 'key' => '',
                 'cert_path' => 'path/to/your/cert.pem',
                 'key_path' => 'path/to/your/key', // XXX: absolute path£¡£¡£¡£¡
                 // 'device_info'     => '013467007045764',
                 // 'sub_app_id'      => '',
                 // 'sub_merchant_id' => '',
                 // ...
             ],     
            'guzzle' => [
                'timeout' => 5,
                //'verify' => false,
            ],         
            ]
        ], 



    ],

    'modules' => [
        'comments' => [
            'class' => 'yeesoft\comments\Comments',
            'userModel' => 'yeesoft\models\User',
            'userAvatar' => function ($user_id) {
                $user = yeesoft\models\User::findIdentity((int)$user_id);
                if ($user instanceof yeesoft\models\User) {
                    return $user->getAvatar();
                }
                return false;
            }
        ],


        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['*'],
            'generators' => [
                'yee-crud' => [
                    'class' => 'yeesoft\generator\crud\Generator',
                    'templates' => [
                        'default' => '@vendor/yeesoft/yii2-yee-generator/crud/yee-admin',
                    ]
                ],
            ],
        ],

        'eav' => [
            'class' => 'yeesoft\eav\EavModule',
        ],

        'block' => [
            'class' => 'yeesoft\block\BlockModule',
        ],

        'carousel' => [
            'class' => 'yeesoft\carousel\CarouselModule',
        ],

    ],







];
