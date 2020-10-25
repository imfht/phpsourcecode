# 项目说明

## 简介

提供旅游路线自定制与分享服务的站点，及相应 [Android App](https://git.oschina.net/spl/GoTravelling-AndroidApp) 的后台.

## 相关技术点
* 项目的后台部分基于 [Laravel 5.0](http://www.golaravel.com/laravel/docs/5.0/) 构建
* 数据处理及存储，基于 [MongoDB](http://www.mongodb.org) , 且项目中Laravel相关代码中出现的的Eloquent被设定为由 [Laravel-MongoDB](https://github.com/jenssegers/Laravel-MongoDB) 中相应类提供

## 初始化步骤

**备注** : 为成功初始化，必须安装 [Composer](http://getcomposer.org/) 、[Node](https://nodejs.org/) 环境

在命令行/终端输入下列指令，或进行相关操作：

* `composer install` : 此命令用于通过Composer安装Laravel框架本身，以及项目所依赖的第三方类或本项目所延伸的子项目
* `bower install`: 此命令用于通过bower安装前端用到的第三方类库或本项目所延伸的子项目
* `npm install`:  此命令用于安装npm包
* `grunt`: 此命令用于构建项目相关的前端文件
* 设置数据库链接，可以参考 [Laravel 5中的环境配置](http://www.golaravel.com/laravel/docs/5.0/configuration/)
* 执行数据库迁移： `php artisan migrate`

关于环境搭建的更多问题及解决方案，可参考 [项目Wiki](https://git.oschina.net/spl/GoTravelling-WebServer/wikis/环境搭建的常见问题)

## 其他说明
待完善...
