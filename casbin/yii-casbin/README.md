# Yii-Casbin

[![Build Status](https://travis-ci.org/php-casbin/yii-casbin.svg?branch=master)](https://travis-ci.org/php-casbin/yii-casbin)
[![Coverage Status](https://coveralls.io/repos/github/php-casbin/yii-casbin/badge.svg)](https://coveralls.io/github/php-casbin/yii-casbin)
[![Latest Stable Version](https://poser.pugx.org/casbin/yii-adapter/v/stable)](https://packagist.org/packages/casbin/yii-adapter)
[![Total Downloads](https://poser.pugx.org/casbin/yii-adapter/downloads)](https://packagist.org/packages/casbin/yii-adapter)
[![License](https://poser.pugx.org/casbin/yii-adapter/license)](https://packagist.org/packages/casbin/yii-adapter)

Use [Casbin](https://github.com/php-casbin/php-casbin) in Yii 2.0 PHP Framework.

## Installation

### Getting Composer package

Require this package in the `composer.json` of your Yii 2.0 project. This will download the package.

```
composer require casbin/yii-adapter
```

### Configuring application

To use this extension, you have to configure the `Casbin` class in your application configuration:

```php
return [
    //....
    'components' => [
        'casbin' => [
            'class' => '\CasbinAdapter\Yii\Casbin',
            
            /*
             * Yii-casbin model setting.
             */
            'model' => [
                // Available Settings: "file", "text"
                'config_type' => 'file',
                'config_file_path' => '/path/to/casbin-model.conf',
                'config_text' => '',
            ],

            // Yii-casbin adapter .
            'adapter' => '\CasbinAdapter\Yii\Adapter',

            /*
             * Yii-casbin database setting.
             */
            'database' => [
                // Database connection for following tables.
                'connection' => '',
                // CasbinRule tables and model.
                'casbin_rules_table' => '{{%casbin_rule}}',
            ],
        ],
    ]
];
```


## Usage

This provides the basic access to Casbin via the `casbin` application component:

```php

$casbin = \Yii::$app->casbin;

$sub = 'alice'; // the user that wants to access a resource.
$obj = 'data1'; // the resource that is going to be accessed.
$act = 'read'; // the operation that the user performs on the resource.

if (true === $casbin->enforce($sub, $obj, $act)) {
    // permit alice to read data1
} else {
    // deny the request, show an error
}

```

## Define your own model.conf

[Supported models](https://github.com/php-casbin/php-casbin#supported-models).

## Learning Casbin

You can find the full documentation of Casbin [on the website](https://casbin.org/).
