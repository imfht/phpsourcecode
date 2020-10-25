<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

$config = [
    'id' => 'pfast-backend',
//     'homeUrl'=>'site/index',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'w3BnewAWmCrjijzkiLucYD5Ty1Ym_V9F',
        ],
        
//         'urlManager' => [
//             'enablePrettyUrl' => true,
//             'showScriptName' => false,
//         ],
        
        'user'=>[
            'class'=>'yii\web\User',
            'identityClass' => 'backend\models\AdminUser',
            'enableAutoLogin' => true,
            'enableSession'=>true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
    'language'=>'zh-CN'
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'model' => [
                'class' => 'common\generators\model\Generator2',
                'templates' => [
                    'adminlte' => '../../common/generators/model/adminlte',
                ]
            ],
            'crud' => [
                'class' => 'common\generators\crud\Generator',
                'templates' => [
                    'adminlte' => '../../common/generators/crud/adminlte',
                ]
            ],
        ]
    ];
}

return $config;
