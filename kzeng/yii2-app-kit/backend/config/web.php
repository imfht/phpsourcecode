<?php

$config = [
        'language' => 'zh-CN',

    'homeUrl'=>Yii::getAlias('@backendUrl'),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute'=>'timeline-event/index',
    
    'controllerMap'=>[
        'file-manager-elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['manager'],
            'disabledCommands' => ['netmount'],
            'roots' => [
                [
                    'baseUrl' => '@storageUrl',
                    'basePath' => '@storage',
                    'path'   => '/',
                    'access' => ['read' => 'manager', 'write' => 'manager']
                ]
            ]
        ]
    ],
    'components'=>[
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => getenv('BACKEND_COOKIE_VALIDATION_KEY'),
            'parsers' => [
                // 因为模块中有使用angular.js  所以该设置是为正常解析angular提交post数据
              'application/json' => 'yii\web\JsonParser',
             ],
        ],


        'assetManager' => [
            'linkAssets' => false
        ],

        'user' => [
            'class'=>'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
    ],
    'modules'=>[
        'i18n' => [
            'class' => 'backend\modules\i18n\Module',
            'defaultRoute'=>'i18n-message/index'
        ],
        'wechat' => [ // 指定微信模块
            'class' => 'callmez\wechat\Module',
            //'adminId' => 1 // 填写管理员ID, 该设置的用户将会拥有wechat最高权限, 如多个请填写数组 [1, 2]
        ]
    ],
    'as globalAccess'=>[
        'class'=>'\common\behaviors\GlobalAccessBehavior',
        'rules'=>[
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['?'],
                'actions'=>['login']
            ],
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions'=>['logout']
            ],
            [
                'controllers'=>['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions'=>['error']
            ],
            [
                'controllers'=>['debug/default'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['user'],
                'allow' => true,
                'roles' => ['administrator'],
            ],
            [
                'controllers'=>['user'],
                'allow' => false,
            ],
            [
                'allow' => true,
                'roles' => ['manager'],
            ]
        ]
    ]
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class'=>'yii\gii\generators\crud\Generator',
                'templates'=>[
                    'yii2-app-kit' => Yii::getAlias('@backend/views/_gii/templates')
                ],
                'template' => 'yii2-app-kit',
                'messageCategory' => 'backend'
            ]
        ]
    ];
}

return $config;
