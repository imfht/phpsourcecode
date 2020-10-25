<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'backend',
    'homeUrl' => '/admin',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'settings' => [
            'class' => 'yeesoft\settings\SettingsModule',
        ],
        'menu' => [
            'class' => 'yeesoft\menu\MenuModule',
        ],
        'translation' => [
            'class' => 'yeesoft\translation\TranslationModule',
        ],
        'user' => [
            'class' => 'yeesoft\user\UserModule',
        ],
        'media' => [
            'class' => 'yeesoft\media\MediaModule',
            'routes' => [
                'baseUrl' => '', // Base absolute path to web directory
                'basePath' => '@frontend/web', // Base web directory url
                'uploadPath' => 'uploads', // Path for uploaded files in web directory
            ],
        ],
        'post' => [
            'class' => 'yeesoft\post\PostModule',
            'viewList' => [
                'post' => 'Post View', // 默认 勿删
                'post_test' => 'View Label 2',
            ],
            'layoutList' => [
                'main' => '右侧边栏', // 默认 勿删
                'dark_layout' => '左侧边栏',
            ],
        ],
        'page' => [
            'class' => 'yeesoft\page\PageModule',
        ],
        'seo' => [
            'class' => 'yeesoft\seo\SeoModule',
        ],
        'comment' => [
            'class' => 'yeesoft\comment\CommentModule',
        ],
        'mp' => [
            'class' => '\backend\modules\mp\Module',
        ],
        'mp_menu' => [
            'class' => '\backend\modules\mp_menu\MenuModule',
            // 'basePath' => '@backend/modules/mp_menu',
        ],
    ],
    'components' => [
        'request' => [
            'baseUrl' => '/admin',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => '@yeesoft/yii2-yee-core/assets/theme/bootswatch/custom',
                    'css' => ['bootstrap.css']
                ],
            ],
        ],
        'urlManager' => [
            'class' => 'yeesoft\web\MultilingualUrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'multilingualRules' => false,
            'rules' => array(
                //add here local frontend controllers
                //'<controller:(test)>' => '<controller>/index',
                //'<controller:(test)>/<id:\d+>' => '<controller>/view',
                //'<controller:(test)>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                //'<controller:(test)>/<action:\w+>' => '<controller>/<action>',
                //yee cms and other modules routes
                '<module:\w+>/' => '<module>/default/index',
                '<module:\w+>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
                '<module:\w+>/<action:(create)>' => '<module>/default/<action>',
                '<module:\w+>/<controller:\w+>' => '<module>/<controller>/index',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
            )
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
