<?php
$config = [

    'modules' => [

        'filemanager' => [
            'class' => 'pendalf89\filemanager\Module',

            // Upload routes
            'routes' => [
                // Base absolute path to web directory
                'baseUrl' => '',
                // Base web directory url
                'basePath' => '@backend/web',
                // Path for uploaded files in web directory
                'uploadPath' => 'uploads',
            ],
            // Thumbnails info
            'thumbs' => [
                'small' => [
                    'name' => 'small',
                    'size' => [100, 100],
                ],
                'medium' => [
                    'name' => 'med',
                    'size' => [300, 200],
                ],
                'large' => [
                    'name' => 'big',
                    'size' => [500, 400],
                ],
            ],
        ],
    ],

    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => true,
            'appendTimestamp' => YII_ENV_DEV
        ]
    ],
    'as locale' => [
        'class' => 'common\behaviors\LocaleBehavior',
        'enablePreferredLanguage' => true
    ]
];

if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.33.1', '172.17.42.1', '172.17.0.1'],
    ];
}

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.33.1', '172.17.42.1', '172.17.0.1'],
    ];
}


return $config;
