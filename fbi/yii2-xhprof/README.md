#yii2-xhprof
xhprof for yii2
========================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require fbi/yii2-xhprof "*"
```

for dev-master

```
php composer.phar require fbi/yii2-xhprof "dev-master"
```

or add

```
"fbi/yii2-xhprof": "*"
```

to the require section of your `composer.json` file.


Usage
-----

1. add the following code to your entry script,for example: index.php
```php
defined('YII_PROFILE') or define('YII_PROFILE',true);
```

2. then modify your application configuration as follows at the end of your config file:

```php
if (YII_PROFILE){
	$config['bootstrap'][] = 'xhprof';
	$config['modules']['xhprof'] = [
		'class'=>'fbi\xhprof\Module',
		'frequency'=>1000,//record rate
		'minExcutionTime'=>1,//
		//'name'=>'linkserver',//xhprof source,default value: Yii::$app->id
		//'dir'=>'/tmp',//record path ,default value: @runtime/xhprof/
	];
}
```

3. then you browse the profs var http://your.site.name/index.php?r=xhprof

[sanwkj@163.com](mailto:sanwkj@163.com?subject=yii2-xhprof)