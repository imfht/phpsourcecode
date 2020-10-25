# CYAdmin说明

CYAdmin，基于ThinkPHP6+aceadmin的通用后台管理系统。

## 环境需求：

PHP 7.1.0+，PDO扩展，MySQL5.0+

## 安装说明：


1、下载本源码至服务器。

2、将.bak.env重命名为.env，并打开此文件修改数据库配置，主要有服务器地址、数据库名、数据库用户名、数据库密码、数据库连接端口、数据表前辍等配置。

3、使用数据迁移命令将数据导入数据库，命令如下：

```
php think migrate:run
```

注：虚拟机可以手动导入“database/database.sql”到数据库进行安装。

4、完成安装，打开网站URL查看效果。


## 后台地址：

http://url/admin/
若安装后打不开，请注意ThinkPHP的Rewrite规则在当前服务器是否适用。

## 管理员用户及密码：

admin admin

## 其它

为什么要做这个？这个项目其实是从ThinkPHP5内测版就开始在弄了，主要是学习ThinkPHP，之前有个基于3.2.x版的，但没开源出来。

## 演示地址

http://cyadmin.mychunyan.com/admin

admin admin

## QQ群号
QQ群号：254929907，欢迎加群交流。

## 免责申明

本系统（CYAdmin）是基于ThinkPHP6+aceadmin的通用后台管理系统，任何基于本系统二次开发所得的系统与本系统无关，本系统开发者也不承担相应法律责任，特此申明。