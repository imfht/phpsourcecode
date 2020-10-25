Admin logging for Yii 2
========================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require fbi/yii2-admin-logging "*"
```

for dev-master

```
php composer.phar require fbi/yii2-admin-logging "dev-master"
```

or add

```
"fbi/yii2-admin-logging": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
	'components' => [
		....
		'user' => [
         	'class'=>'fbi\adminLogging\components\User',
         	....
        ]
        ....
        'request' => [
         	'class'=>'fbi\adminLogging\components\Request',
	],
];
```

and change your backend contorllers like follows:

```
....
use fbi\adminLogging\controllers\Controller;//this line
....
class SiteController extends Controller{
	...
}
```

and if you want to log something in the application:

```
Yii::$app->user->log('mixed variable,anything you want to log');
```

[sanwkj@163.com](mailto:sanwkj@163.com?subject=yii2-admin-logging)