# NB Framework

[![Latest Version](https://img.shields.io/badge/beta-v2.0.0-green.svg?maxAge=2592000)](https://github.com/nbcx/framework/releases)
[![Php Version](https://img.shields.io/badge/php-%3E=7.0-brightgreen.svg?maxAge=2592000)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=2.1.3-brightgreen.svg?maxAge=2592000)](https://github.com/swoole/swoole-src)
[![NB Wiki](https://img.shields.io/badge/docs-passing-green.svg?maxAge=2592000)](https://wiki.nb.cx)
[![NB Framework License](https://img.shields.io/hexpm/l/plug.svg?maxAge=2592000)](https://github.com/nbcx/framework/master/LICENSE)

NB Framework 是一款深度兼容Swoole和FPM双模式运行，并且轻量，全功能，自由度高，无上手门槛的PHP开发框架！


## 优势:
- 自由在swoole和php-fpm两种运行方式之间切换
- 高度自由的自定义开发模式，打造你自己独特的项目风格
- 独立的DEBUG调试页面，使多端合作开发简单方便
- 简单而强大的配置方式

## 特性:
NB提供了WEB开发中几乎所有的基础功能，且依旧保持轻量。你可以很容易的从头到尾构建一个强大的网站应用。
- 全局的依赖注入容器
- 支持 Swoole 扩展
- 可扩展的驱动式组件开发
- 国际化(i18n)支持
- 数据库 ORM
- 强大的调试系统
- 视图模版
- 快速灵活的参数验证器
- 多类型的缓存驱动
- 多模块支持
- 命令行运行模式
- 高内聚低耦合
- 内置协程 HTTP, TCP, WebSocket 网络服务器
- 协程 Mysql, Redis 客户端
- 强大的命令行工具
- Composer管理


## 快速使用

### 一种最简单最快速的使用方式

1.在你的应用目录下新建如下结构：
```
/home/www/demo/                      项目根目录
├─application                        应用目录
│  ├─controller                      控制器目录
│  │  └─Index.php                    测试控制器
├─public                                
│  └─index.php                       入口文件
├─nb                                 框架系统目录
├─tmp                                缓存目录,需要可读写权限                                
```
> `框架系统目录`就是克隆或下载`framework`到根目录，并改名为`nb`即可。

2.编写入口文件：
```php
<?php
//项目的根路径
define('_APP_',__DIR__.'/..');

//加载初始化文件
include ('../nb/boot.php');

\nb\Config::register();

\nb\Dispatcher::run();
```
3.编写测试控制器
```php
<?php
namespace controller;
class Index {

    public function index() {
        echo '<h1>Welcome to use NB Framework!</h1>';
    }
}
```
4.配置Nginx
```
server {
    listen 80;
    server_name demo.ol.cx;
    index index.php;
    root /home/www/demo/public/;

    location / {
        rewrite ".+" "/index.php" last;
    }

    location ~ .*\.php {
        fastcgi_pass   127.0.0.1:9100;
        fastcgi_index index.php;
        include fastcgi.conf;
    }

    location ~ .*\.(svg|woff2|map|html|woff|ttf|ico|css|js|gif|jpg|jpeg|png|bmp|swf)$ {
        expires 90d;
    }
}
```
5.重启nginx，在浏览器访问`demo.ol.cx`,输出`Welcome to use NB Framework`则表示成功

### 使用Swoole启动应用,确保swoole已经安装成功！
1.再上面的目录下添加如下结构
```
/home/www/demo/                      项目根目录
├─application                        应用目录
│ ...
├─bin                                   
│  └─server                          服务启动文件
├─config.inc.php                     框架配置文件
```
2.编写服务启动文件
```php
#!/usr/bin/env php
<?php
//项目的根路径
define('_APP_',__DIR__.'/..');

//加载初始化文件
include ('../nb/boot.php');

\nb\Config::register();
\nb\Console::run('server');
```
> 编写完成后，给其执行权限：`chmod +x server`

3.编写框架配置文件
```php
<?php
return [
    'debug' => true,
    
    'server' => [
        'driver'=>'http',
        'host'=>'0.0.0.0',
        'port'=>9501,
        'max_request'=>100,//worker进程的最大任务数
        'worker_num'=>2,//设置启动的worker进程数。
        'dispatch_mode'=>2,//据包分发策略,默认为2
        'debug_mode'=>3,
        'enable_gzip'=>0,//是否启用压缩，0为不启用，1-9为压缩等级
        'log_file'=>__APP__.'tmp'.DS.'swoole-http.log',
        'enable_pid'=>__APP__.'tmp'.DS.'swoole-http.pid',
        'daemonize'=>false,
        //异步任务处理配置
        'task_worker_num'=>2,
    ]
];
```
4.运行
```
# 如果你的php命令已经加入系统环境里了，可以直接如下执行
./server start

# 如果没有再系统环境里，则需要使用完整的php命令路径来执行，如下，注意使用自己的路径
/usr/bin/php server start
```

5.看控制台输出，并浏览器访问`127.0.0.1:9501`,输出`Welcome to use NB Framework`则表示成功.


更多功能，可以查看[开发手册](https://wiki.nb.cx) 。

## 协议
NB Framework的开源协议为apache 2.0，详情参见LICENSE。

## 其他
- 官方QQ交流群：1985508
- 项目官网主页：[https://nb.cx](https://nb.cx)
- 文档维护地址：[https://wiki.nb.cx](https://wiki.nb.cx) 

欢迎各位老铁进群一起写代码。如果感兴趣，也请帮点个star吧！

    
    