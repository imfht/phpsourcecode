---
title: 结构
lang: zh-CN
---

# 结构

## 目录结构

Yesf应用的基本目录结构如下：

```
|+ application //应用目录
  |+ Config //配置文件
    |- Project.php //项目相关配置文件
    |- Server.php //服务器相关配置文件
    |- env.ini //其他环境配置
  |+ Model //Model目录
    |- test.php //某个Model
  |+ Module //模块目录
    |+ Index //例如有一个名为Index的模块
      |+ Controller //Index模块的控制器
        |- Index.php //默认控制器
      |+ View //视图（模板）目录
        |+ Index //对应Index控制器
          |- Index.phtml //对应Index方法
          |- User.phtml //对应User方法
  |+ Library //本地类库
  |+ Plugin //插件目录
| + vendor //Composer生成目录
| - start.php //入口文件
| - composer.json //Composer文件
```

除了`Config`、`Module`外，其他目录名称都可以自行定义

## 入口文件

Yesf不同于传统的PHP框架，Yesf将会持久运行，这也就意味着Yesf不需要依赖于nginx/Apache。入口文件只用做启动，内容如下：

```php
<?php
error_reporting(E_ALL &~ E_NOTICE);
require('vendor/autoload.php');
//应用所在目录
define("APP_PATH",  __DIR__ . '/application/');
//初始化
$app = new Yesf\Yesf();
$app->run(APP_PATH . "/Config/env.ini");
```

之后，在shell中执行`php start.php`即可运行

## 重写规则

尽管Swoole并不依赖于nginx/Apache，但是仍推荐使用nginx来处理请求，例如：（假设`/web/wwwroot/public/`存放一些静态文件）

```
server {
  root /web/wwwroot/public/;
  server_name example.com;
  underscores_in_headers on;

  proxy_http_version 1.1;
  proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
  proxy_set_header X-Real-IP $remote_addr;
  proxy_set_header Host $host;
  proxy_set_header Connection "keep-alive";

  location / {
    try_files /$uri $uri/ @proxy;
  }

  location @proxy {
    proxy_pass http://127.0.0.1:9000;
  }
}
```

## 配置文件

Yesf将配置文件分为三类：

* Project.php 项目配置

项目配置中一般会存放项目相关的配置，例如项目名称、项目的namespace等，一般来说，不论在任何机器上部署，此处的配置都是相同的。

此配置中部分项目支持热重载。

* Server.php 服务器配置

主要是Swoole相关配置，一般来说，不同服务器上此处配置可能不同

* env.ini 环境配置

此配置支持热重载。用于一些其他配置，如数据库连接信息等。

配置文件的详细说明，请参考[配置](configuration.md)章节

## 控制器

默认的模块/控制器/动作, 都是以`Index`命名的。其中，默认的模块可以在配置文件中进行修改

控制器处于`应用的命名空间\Module\模块名称\Controller\`，名称首字母大写

动作的命名规则是"名字+Action"。

例如，这是一个控制器：

```php
namespace YesfApp\Module\Index\Controller\Index;

class Index {
   	public function IndexAction($request, $response) { //默认Action
       	$response->assign("content", "Hello World");
   	}
}
```

注意：

* 所有请求均会传入`$request`和`$response`两个参数，这两个参数分别包括了请求的基本信息和回复请求的方法。详细说明请参考《请求处理》
* 所有echo、var_dump等输出函数，均不会对浏览器输出任何内容
* **不要**抛出异常

## 视图文件

Yesf自带的视图引擎十分简单

视图文件的路径是在相应模块目录下的View目录中，文件名称与动作名称相同。

例如，Index模块，User控制器，Login动作的默认模板为`application/Module/Index/View/User/Login.phtml`

```html
<html>
 <head>
   <title>Hello World</title>
 </head>
 <body>
  <?=$content?>
 </body>
</html>
```

## 运行

使用`php start.php`启动，之后，在浏览器中访问`http://127.0.0.1:9000`，即可看到“Hello World”的输出