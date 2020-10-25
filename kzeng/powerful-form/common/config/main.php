<?php
return [
    'language' => 'zh-CN',
    //'sourceLanguage' => 'zh-CN',
    
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
