<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            // 'dsn' => 'mysql:host=localhost;dbname=yii2cms',
            'username' => 'root',
            'dsn' => 'mysql:host=120.27.202.235;dbname=yii2cms',
            'password' => 'G9mn4[K3',
            // 'password' => 'password#1',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            #'transport' => [
            #    'class' => 'Swift_SmtpTransport',
            #    'host' => 'smtp.gmail.com',
            #    'username' => 'yourname@gmail.com',
            #    'password' => 'yourpassword',
            #    'port' => '587',
            #    'encryption' => 'tls',
            #],

            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',
                'username' => 'zengkai001@qq.com',
                'password' => 'jfpvkrfwcopibgdb',
                'port' => '465',
                'encryption' => 'ssl',
            ],

            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['zengkai001@qq.com'=>'admin'],
            ],

            'htmlLayout' => '@vendor/yeesoft/yii2-yee-auth/views/mail/layouts/html',
            'textLayout' => '@vendor/yeesoft/yii2-yee-auth/views/mail/layouts/text',
        ],
    ],
];
