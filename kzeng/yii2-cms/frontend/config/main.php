<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'frontend',
    'homeUrl' => '/',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'auth' => [
            'class' => 'yeesoft\auth\AuthModule',
        ],
    ],
    'components' => [
        'view' => [
            // 'theme' => [
            //     'class' => 'frontend\components\Theme',
            //     'theme' => 'cosmo', //cerulean, cosmo, default, flatly, readable, simplex, united
            // ],
            'as seo' => [
                'class' => 'yeesoft\seo\components\SeoViewBehavior',
            ]
        ],
        'seo' => [
            'class' => 'yeesoft\seo\components\Seo',
        ],
        'request' => [
            'baseUrl' => '',
            'enableCsrfValidation' => false,
        ],
        'urlManager' => [
            'class' => 'yeesoft\web\MultilingualUrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            //'enablePrettyUrl' => false,

            'rules' => array(
                '<module:auth>/<action:(logout|captcha)>' => '<module>/default/<action>',
                '<module:auth>/<action:(oauth)>/<authclient:\w+>' => '<module>/default/<action>',
            ),
            'multilingualRules' => [
                '<module:auth>/<action:\w+>' => '<module>/default/<action>',
                '<controller:(categories|category|tag)>/<slug:[\w \-]+>' => '<controller>/index',
                '<controller:(categories|category|tag)>' => '<controller>/index',
                '<controller:post>/<id:\d+>' => '<controller>/index',
                '<slug:[\w \-]+>' => 'site/index/',
                '/' => 'site/index',
                '<action:[\w \-]+>' => 'site/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
            'nonMultilingualUrls' => [
                'auth/default/oauth',
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
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
    ],
    'params' => $params,
];
