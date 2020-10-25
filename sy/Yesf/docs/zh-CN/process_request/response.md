---
title: Response：响应
lang: zh-CN
---

# Response：响应

用于响应一个请求

## header

`header('名称', '内容')`，如：`$response->header('Content-Type', 'text/html');`

特别的，你可以使用`mimeType`来发送`Content-Type`头，如`$response->mimeType('html');`

## HTTP状态码

`status(状态码)`

## 输出至浏览器

`write('内容')`，如：`$response->write('Hello World');`

特别的，你可以使用`json()`来输出JSON内容，如：`$response->json($res);`

## 发送文件

当文件较大时，你可以使用此方法发送文件，而无需将其读入内存中，如：

```php
$response->mimeType('zip');
$response->sendfile('/path/to/file.zip');
```

## Cookie

`cookie(Cookie信息)`

| 名称 | 类型 | 内容 |
| --- | ---- | --- |
| name | string | 名称 |
| value | string | 内容 |
| expire | int | 过期时间，-1为失效，不传递或0为SESSION，其他为`当前时间+$expire` |
| path | string | Cookie有效的服务器路径。若不传递，则从环境配置读取 |
| domain | string | Cookie的有效域名。若不传递，则从环境配置读取 |
| httponly | bool | 是否仅http传递，默认为否 |

如：

```php
$response->cookie([
	'name' => 'token',
	'value' => '123456',
	'expire' => '3600', //一小时有效
	'path' => '/',
	'httponly' => true
])
```

## 模板

### 注册一个模板变量

`assign('名称', '内容')`，如：`$response->assign('name', 'Admin');`

### 设置模板引擎

* 设置全部：`Response::setTemplateEngine(MyTemplate::class);`
* 设置当前响应：`$response->setCurrentTemplateEngine(MyTemplate::class);`

### 关闭模板自动渲染

在项目配置中：

```php
'view' => [
    'auto' => false
]
```

当前响应：`$response->disableView();`

### 渲染指定模板并输出至浏览器

默认会自动渲染View目录下同名的模板，使用此方法并不会关闭默认的渲染。

`display('模板路径，相对于当前模块的View目录')`

如：`$response->display('user/view');`