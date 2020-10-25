---
title: 配置
lang: zh-CN
---

# 配置

## 综述

Yesf将配置文件分为三类：

* Project.php 项目配置

项目配置中一般会存放项目相关的配置，例如项目名称、项目的namespace等，一般来说，不论在任何机器上部署，此处的配置都是相同的。

此配置中部分项目支持热重载。

* Server.php 服务器配置

主要是Swoole相关配置，一般来说，不同服务器上此处配置可能不同

* env.ini 环境配置

此配置支持热重载。用于一些其他配置，如数据库连接信息等。

一般来说，Yesf推荐使用ini作为配置文件。Yesf同时也支持QConf、Yaconf，可以将配置文件与程序分离开。

默认情况下，Yesf将会采用`product`节的配置。此内容可以通过`APP_ENV`常量修改。或使用`Yesf::app()->setEnvironment('product')`修改。

配置文件支持继承。配置文件只会在载入时进行解析，解析后的内容将会持久化的保存在内存中，因此，不用担心配置文件太大造成解析性能问题。

## 配置的读取

配置可以通过`Yesf::app()->getConfig($key)`读取，例如`Yesf::app()->getConfig('name')`

读取不存在的配置时，将会返回`NULL`

## 配置继承

Yesf的配置支持简单的继承，使用冒号分隔。例如，下面示例中，`product`继承了`base`的配置

```ini
[base]
name="yesf"
[product:base]
database.host="127.0.0.1"
database.port=3306
```

此时，假定环境为`product`，可以使用`name`获取到“yesf”

## 项目配置

```php
return [
  'name' => 'yesf', //应用名称，用于设定进程名
  'namespace' => 'YesfApp\\', //应用的namespace，用于自动加载
  'charset' => 'utf-8', //应用的编码，数据库等均以此编码为准
  'router' => [
    'map' => 'true', //是否开启map解析
    'extension' => true //路由是否包括扩展名。为1时，会自动去掉扩展名，并放入`$request->extension`中
  ],
  'modules' => ['index', 'admin'], //有效的模块列表
  'module' => 'index', //自定义默认模块，默认为`index`
  'view' => [
    'auto' => true, //是否开启自动渲染
    'extension' => 'phtml' //视图文件扩展名，默认为phtml
  ]
];
```

## 服务器配置

```php
return [
  'ip' => "0.0.0.0", //监听IP
  'port' => 9501, //监听端口
  'pid' => "/tmp", //PID存放目录
  'advanced' => [
    'daemonize' => 0, //是否为守护模式运行
    'dispatch_mode' => 2, //分发模式
    'package_max_length' => 2097152, //1024 * 1024 * 2
    'buffer_output_size' => 3145728, //1024 * 1024 * 3
    'pipe_buffer_size' => 33554432, //1024 * 1024 * 32
    'open_tcp_nodelay' => 1,
    'heartbeat_check_interval' => 5, //心跳检测
    'heartbeat_idle_time' => 11, //心跳检测
    'open_cpu_affinity' => 1, //CPU亲和
    'reactor_num' => 2, //reactor线程数，建议设置为CPU核数 x 2
    'worker_num' => 4, //守护进程数，详情见http://wiki.swoole.com/wiki/page/275.html
    'task_worker_num' => 2, //Task进程数，详情见http://wiki.swoole.com/wiki/page/276.html
    'max_request' => 0, 
    'task_max_request' => 4000, 
    'backlog' => 3000, 
    'log_file' => "/tmp/sw_server.log", //swoole系统日志，任何代码内echo都会在这里输出
    'task_tmpdir' => "/tmp/swtasktmp/" //task投递内容过长时，会临时保存在这里
  ]
];
```

## 数据库

首先配置连接池的最大连接数和最小连接数

```ini
connection.default.min=1
connection.default.max=5
```

然后，配置单个数据库连接。其中，`my`为自定义名称。示例中是mysql连接

```ini
connection.my.adapter=mysql
connection.my.driver=mysql
connection.my.host=localhost
connection.my.port=3306
connection.my.user=root
connection.my.password=123456
connection.my.database=test
```

最后，配置默认使用的数据库连接

```ini
database.default=my
```
