# Tencent Xinge PHP SDK for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/freyo/xinge.svg?style=flat-square)](https://packagist.org/packages/freyo/xinge)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/freyo/xinge.svg?style=flat-square)](https://packagist.org/packages/freyo/xinge)

<img src="https://mc.qcloudimg.com/static/img/3c4f02851231c6238cd7681555ace547/XGPush.svg" width="220" height="220">

腾讯移动推送（XinGe Push，即 XGPush）

This package makes it easy to send notifications using Tencent Xinge with Laravel.

## Installation

You can install this package via composer:

``` bash
composer require freyo/xinge
```

Next add the service provider and facade to your `config/app.php`:

```php
...
'providers' => [
    ...
    Freyo\Xinge\ServiceProvider::class,
],
'aliases' => [
    ...
    'Xinge' => Freyo\Xinge\Facade::class,
],
...
```

**Laravel 5.5 uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.**

### Setting up the Xinge service

You will need to [create](http://xg.qq.com/) a Xinge app in order to use this channel. Within in this app you will find the `access id and access secret`. Place them inside your `.env` file. In order to load them, add this to your `config/services.php` file:

```php
...
'xinge' => [
    'android' => [
        'access_id'    => env('XINGE_ANDROID_ACCESS_ID'),
        'secret_key'   => env('XINGE_ANDROID_ACCESS_KEY')
    ],
    'ios' => [
        'access_id'    => env('XINGE_IOS_ACCESS_ID'),
        'secret_key'   => env('XINGE_IOS_ACCESS_KEY')
    ],
]
...
```

This will load the Xinge app data from the `.env` file. Make sure to use the same keys you have used there like `XINGE_IOS_ACCESS_ID`.

## Usage

#### Notification

Follow [Laravel's documentation](https://laravel.com/docs/notifications) to add the channel to your Notification class.

Example: [AndroidPushSingleAccount](https://github.com/freyo/xinge/blob/master/src/Notifications/AndroidPushSingleAccount.php), [iOSPushSingleAccount](https://github.com/freyo/xinge/blob/master/src/Notifications/iOSPushSingleAccount.php).

#### Facade

```php
Xinge::android()->PushSingleDevice($deviceToken, $message);
Xinge::android()->PushSingleAccount($deviceType, $account, $message);
Xinge::android()->PushAllDevices($deviceType, $message);
Xinge::android()->PushTags($deviceType, $tagList, $tagsOp, $message);
Xinge::android()->PushAccountList($deviceType, $accountList, $message);

Xinge::ios()->PushSingleDevice($deviceToken, $message, $environment = 0);
Xinge::ios()->PushSingleAccount($deviceType, $account, $message, $environment = 0);
Xinge::ios()->PushAllDevices($deviceType, $message, $environment = 0);
Xinge::ios()->PushTags($deviceType, $tagList, $tagsOp, $message, $environment = 0);
Xinge::ios()->PushAccountList($deviceType, $accountList, $message, $environment = 0);
```

[FULL API DOCUMENT](http://docs.developer.qq.com/xg/server_api/rest.html)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
