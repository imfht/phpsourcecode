<p align="center">
    <img src="hopephp/logo.png" alt="HopePHP" align="center" />
</p>
<p align="center">HopePHP是一个轻量级、MVC设计模式的开发框架。</p>

## 前言
轮子，该造的还是要造。HopePHP没有比较前沿、特色的东西，但却是phper学习MVC编程模式的一个好帮手。
HopePHP是作者工作之余开发的一个轻型框架，作者我要做的就是让它更精良，同时，如果你选择了HopePHP，你就要知道，很多东西需要你自己来完成。

## 特性
- 基于命名空间
- 精简路由功能
- 数据库基于ThinkORM
- 模板引擎基于ThinkTemplate
- 多配置、配置分离
- 惰性加载
- 支持Composer
- 简化扩展机制

> HopePHP的运行环境需要在PHP5.4以上

详细使用方法请查阅 <a href="https://www.kancloud.cn/wispx/hopephp" target="_block">HopePHP 1.0开发文档</a>

## 目录结构
初始的目录结构如下：

~~~
www                 WEB部署目录（或者子目录）
├─app               应用目录
│  ├─controller     控制器目录
│  ├─model          模型目录
│  ├─view           视图目录
│  └─ ...           更多类库目录
│  │
│  ├─common.php     公共函数文件
│  ├─config.php     公共配置文件
│  ├─route.php      路由配置文件
│  └─database.php   数据库配置文件
│
├─public            WEB目录（对外访问目录）
│  ├─static         静态文件目录
│  └─ ...
│  │
│  ├─index.php      入口文件
│  └─.htaccess      用于apache的重写
│
├─hopephp           框架系统目录
│  ├─library        框架类库目录
│  │  ├─think       Think类库包目录
│  │  └─traits      系统Trait目录
│  │
│  ├─temp           系统模板目录
│  ├─helper.php     助手函数文件
│  └─start.php      框架入口文件
│
├─extend            扩展类库目录
├─runtime           应用的运行时目录（可写，可定制）
├─vendor            第三方类库目录（Composer依赖库）
├─composer.json     composer 定义文件
├─LICENSE.txt       授权说明文件
├─README.md         README 文件
~~~

## 命名规范

HopePHP1.0的命名规范遵循PSR-2规范以及PSR-4自动加载规范。

## 感谢
本框架核心代码参考了ThinkPHP，并感谢ThinkPHP提供的ORM和模板引擎

## 版权信息

HopePHP遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2018 by HopePHP

All rights reserved。

更多细节参阅 [LICENSE](LICENSE)

## 联系我
- QQ 1591788658
- Blog http://www.wispx.cn
- Email 1591788658@qq.com
