---
title: 路由
lang: zh-CN
---

# 路由

## 内置路由

> 内置路由位于 src/Http/Router.php

### 前缀

URL若以此开头，则此部分不会参与解析。如：

```php
use Yesf\Http\Router;

function run(Router $router) {
	$router->setPrefix('/user');
}
```

访问`http://example.com/user/index/user/view`时，实际参与解析的是`/index/user/view`。

默认为空。

### Restful风格

支持get、post、put、delete、head、options、connect。如：

```php
$router->get('/user/{id}', [
	'module' => 'index',
	'controller' => 'user',
	'action' => 'view'
], [
	'id' => '(\d+)'
]);
```

这段代码做了：

* URL格式为`user/{id}`
* 将其解析为index模块、user控制器、view功能
* id需要满足正则表达式`/^(\d+)$/`

您也可以使用闭包：

```php
$router->get('/user/{id}/{action}', function($param) {
	return [
		'module' => 'index',
		'controller' => 'user',
		'action' => $param['action']
	];
}, [
	'id' => '(\d+)'
]);
```

您也可以使用any，它的优先级是最低的：

```php
$router->any('/user/{id}', [
	'module' => 'index',
	'controller' => 'user',
	'action' => 'view'
], [
	'id' => '(\d+)'
]);
```

您可以简写第二个参数：

```php
$router->any('/user/{id}', 'index.user.view');
```

### Map

默认的隐式路由，不需要特别配置。将会按`module/controller/action`的规则解析。

如`index/user/view?id=1`会解析至Index模块、User控制器、View功能。

您可以手动关闭它：

在项目配置中：

```php
'router' => [
	'map' => false
]
```

或在代码中：

```php
$router->disableMap();
```

## 自定义路由

可以自定义路由，不使用内置路由。

首先，编写类实现RouterInterface，如：

```php
use Yesf\Http\RouterInterface;

class MyRouter implements RouterInterface {
	public function parse(Request $request) {
		if (strpos($request->server['request_uri'], '/user') === 0) {
			$request->module = 'user';
			$request->controller = 'index';
			$request->action = 'index';
		} else {
			$request->module = 'index';
			$request->controller = 'index';
			$request->action = 'index';
		}
	}
}
```

在Dispatcher上注册：

```php
namespace YesfApp;

use MyRouter;
use Yesf\Http\Dispatcher;

class Configuration {
	public function setRouter(Dispatcher $dispatcher, MyRouter $router) {
		$dispatcher->setRouter($router);
	}
}
```