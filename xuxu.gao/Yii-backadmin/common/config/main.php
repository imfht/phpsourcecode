<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //增加rbac组件
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
];
