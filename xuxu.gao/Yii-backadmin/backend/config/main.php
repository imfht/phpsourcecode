<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
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
        'urlManager' => [

            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'auth/login'=>'Admin/auth/login',
                'user/userupdate/<id:\d+>'=>'Admin/user/userupdate',
                'menu/menuupdate/<id:\d+>'=>'Admin/menu/menuupdate',
                'role/rolepermission/<name:\w+>'=>'Admin/role/rolepermission',
                'role/roleupdate/<name:\w+>'=>'Admin/role/roleupdate',
                'permission/permissionupdate/<name>'=>'Admin/permission/permissionupdate'
            ]
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ]
    ],
    'params' => $params,
];
