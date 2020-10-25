## 简介

* 基于ThinkPHP3.2结合Swoole,快速嵌入TP框架,兼容TP5
* 需要注意异步必须在Cli下进行,Cli下TP默认读取的配置文件为:Application/Common/Conf/config.php
* 有相关疑问请查阅官方文档:https://wiki.swoole.com
* 从未使用过swoole建议先看此新手教程:https://github.com/LinkedDestiny/swoole-doc

## 注意
* 不要在代码中执行sleep以及其他睡眠函数，这样会导致整个进程阻塞
* exit/die是危险的，会导致Worker进程退出
* 可通过register_shutdown_function来捕获致命错误，在进程异常退出时做一些清理工作，具体参考 /wiki/page/305.html
* PHP代码中如果有异常抛出，必须在回调函数中进行try/catch捕获异常，否则会导致工作进程退出
* 不支持set_exception_handler，必须使用try/catch方式处理异常
* Worker进程不得共用同一个Redis或MySQL等网络服务客户端，Redis/MySQL创建连接的相关代码可以放到onWorkerStart回调函数中，具体参考 /wiki/page/325.html

## 全面的WEB开发特性支持

最新的ThinkPHP为WEB应用开发提供了强有力的支持，这些支持包括：

*  MVC支持-基于多层模型（M）、视图（V）、控制器（C）的设计模式
*  ORM支持-提供了全功能和高性能的ORM支持，支持大部分数据库
*  模板引擎支持-内置了高性能的基于标签库和XML标签的编译型模板引擎
*  RESTFul支持-通过REST控制器扩展提供了RESTFul支持，为你打造全新的URL设计和访问体验
*  云平台支持-提供了对新浪SAE平台和百度BAE平台的强力支持，具备“横跨性”和“平滑性”，支持本地化开发和调试以及部署切换，让你轻松过渡，打造全新的开发体验。
*  CLI支持-支持基于命令行的应用开发
*  RPC支持-提供包括PHPRpc、HProse、jsonRPC和Yar在内远程调用解决方案
*  MongoDb支持-提供NoSQL的支持
*  缓存支持-提供了包括文件、数据库、Memcache、Xcache、Redis等多种类型的缓存支持

## 大道至简的开发理念

ThinkPHP从诞生以来一直秉承大道至简的开发理念，无论从底层实现还是应用开发，我们都倡导用最少的代码完成相同的功能，正是由于对简单的执着和代码的修炼，让我们长期保持出色的性能和极速的开发体验。在主流PHP开发框架的评测数据中表现卓越，简单和快速开发是我们不变的宗旨。

## 安全性

框架在系统层面提供了众多的安全特性，确保你的网站和产品安全无忧。这些特性包括：

*  XSS安全防护
*  表单自动验证
*  强制数据类型转换
*  输入数据过滤
*  表单令牌验证
*  防SQL注入
*  图像上传检测

## 商业友好的开源协议

ThinkPHP遵循Apache2开源协议发布。Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。