# epii-admin

epii-admin php开源交互性管理后台框架,完全基于composer模块化构建,基于composer+epii-server+epii-route+Adminlte3.0+Require.js, 让复杂的交互变得更简单！Phper快速搭建交互性平台的开发框架

<p align=center>
<img src="https://gitee.com/epii/EpiiAdmin/raw/master/epiiadmin_logo.jpg" alt="epiiadmin" width="100%">
 
</p>


## 安装方式

### 方式一 composer 安装
composer
```
composer require epii/admin-center
```
或者
```
 "require": {
    "epii/admin-center": ">=0.0.1"
  }
```
入口php文件
```
require_once __DIR__."/../vendor/autoload.php";
//设置默认的命名空间前缀 默认为 app\\
\epii\admin\center\App::getInstance()->init(\init\example::class)->setBaseNameSpace("app\\admin")->run();
```

### 方式二 源码 安装

直接下载本项目源码，入口文件为public/index.php


## 更新
```
composer update epii/admin-center
```

### `epii-admin`是基于`composer+epii-server+epii-route+Adminlte3.0+Require.js`的快速搭建通用管理中心或用户中心的开发框架。

### 解决了什么问题。

> Php在快速开发web应用上优势很明显，尤其是php7.2在性能上大幅度提高，技术的不断更新，新的框架不断产生，使得很多应用层框架来不及更新。我们在遇到新项目时，总是徘徊用旧的框架去开发，还是用到新的框架去做。选择之后很很多人选择了用旧框架，为什么？因为任何一个系统总需要管理中心，或用户中心，而这些界面更多的是前台框架。很多Phper想使用最新的框架（laravel或者thinkphp5.1）去开发，但因前端而止步，不得不为了进度，用旧的系统去改。EpiiAdmin 正是为了解决这些问题。本框架虽然基于tp5.1,但很容易改为laravel。

# 特性
- 功能插件化
- 多管理中心同在，可快速搭建后台管理，用户中心等，根据项目业务需要快速搭建。
- 为Php后台程序员提供最快速的前端实现功能，简单的html代码即可实现复杂的交互体验，几乎无需JS代码。
- 本框架不包含任何应用层模块，纯粹干净。
- 完善的前台组件
    - Adminlte3
    - Bootstrap4
    - Bootstrap-table
    - Layer
    - Jquery
    - Require.js
    - Jquery-addtab.js
    - jquery-validate.js
    - 其它
    - 上述组件虽多，但都已有效集成在系统中，后台人员无需了解其使用方法。
- 支持多种主题。
- 完善的教程和文档。

## 在线演示

[epii-admin演示](http://epiiadmin.chinacloudsites.cn)[bug已修复]

## 在线文档

[epii-admin在线中文文档](https://www.kancloud.cn/rlr123654/epiiadmin-php/704402)[已完结]

### 仓库首页
```
https://github.com/epaii/epii-admin
```

码云（gitee）仓库首页
```
https://gitee.com/epii/EpiiAdmin
```

>注：添加虚拟主机并绑定到EpiiAdmin/public目录 

### 环境要求
```
1. PHP >= 5.5.0 (推荐PHP7.1版本)
2. Mysql >= 5.5.0 (需支持innodb引擎)
3. Apache 或 Nginx
4. php需支持宽展
     pdo_mysql
     MBstring
     CURL
```


# Demo 截图
![screen]
# 教程文档目录
![mulu]

[EpiiAdmin在线中文文档](https://www.kancloud.cn/rlr123654/epiiadmin-php/704402)

[screen]:https://gitee.com/epii/EpiiAdmin/raw/master/screen.png

[mulu]:https://gitee.com/epii/EpiiAdmin/raw/master/mulu1.png

