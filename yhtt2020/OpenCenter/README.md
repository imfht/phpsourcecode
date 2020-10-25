OC3.0开发文档
# 整体概述

## 功能介绍

OpenCenter3.0是一款基于ThinkPHP5.1和LayUIAdmin开发的后台管理系统。主要功能包括管理员和用户管理、权限和权限组、行为日志、积分系统、广告管理等。

## 系统运行介绍

### 环境要求

* **PHP** >= 5.6.0 (推荐PHP7.1以上版本)
* MySQL >= 5.0.0 (需要支持InnoDB引擎)
* Apache 或 Nginx
* PDO PHP Extension
* MBstring PHP Extension
* CURL PHP Extension  

#### 推荐安装

* Composer 第三方包管理工具  

##### 系统安装

运行根目录需指向public文件夹。访问网站后填写相关参数即可进行系统安装。

# 数据库文档

## SQL存放路径

系统安装所需的SQL文件是在/application/admin/command/Install/install.sql。

## 数据库设计习惯

* 所有数据表均使用InnoDB引擎
* 字段类型应在满足要求的前提下占用最小的空间
* 数据表默认的字段包括id,create_time,update_time,status
* create_time和update_time使用int(11),status使用tinyint(1),范围在-1,0,1,2之间

## 所有数据内容

### 数据表

### 关键数据内容



#程序源码文档

## 代码编写习惯

ThinkPHP5.1遵循PSR-2命名规范和PSR-4自动加载规范，并且注意如下规范：  

目录和文件

    目录使用小写+下划线；
    类库、函数文件统一以.php为后缀；
    类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
    类文件采用驼峰法命名（首字母大写），其它文件采用小写+下划线命名；
    类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

函数和类、属性命名

    类的命名采用驼峰法（首字母大写），例如 User、UserType，默认不需要添加后缀，例如UserController应该直接命名为User；
    函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 get_client_ip；
    方法的命名使用驼峰法（首字母小写），例如 getUserName；
    属性的命名使用驼峰法（首字母小写），例如 tableName、instance；
    特例：以双下划线__打头的函数或方法作为魔术方法，例如 __call 和 __autoload；

常量和配置

    常量以大写字母和下划线命名，例如 APP_PATH；
    配置参数以小写字母和下划线命名，例如 url_route_on 和url_convert；
    环境变量定义使用大写字母和下划线命名，例如APP_DEBUG；

数据表和字段

    数据表和字段采用小写加下划线方式命名，并注意字段名不要以下划线开头，例如 think_user 表和 user_name字段，不建议使用驼峰和中文作为数据表及字段命名。

请理解并尽量遵循以上命名规范，可以减少在开发过程中出现不必要的错误。

## 程序目录结构

www  WEB部署目录（或者子目录）  
├─application           应用目录  
│  ├─common             公共模块目录（可以更改）  
│  ├─module_name        模块目录  
│  │  ├─common.php      模块函数文件  
│  │  ├─controller      控制器目录  
│  │  ├─model           模型目录  
│  │  ├─view            视图目录  
│  │  ├─config          配置目录  
│  │  └─ ...            更多类库目录  
│  │  
│  ├─command.php        命令行定义文件  
│  ├─common.php         公共函数文件  
│  └─tags.php           应用行为扩展定义文件  
│  
├─config                应用配置目录  
│  ├─module_name        模块配置目录  
│  │  ├─database.php    数据库配置  
│  │  ├─cache           缓存配置  
│  │  └─ ...              
│  │  
│  ├─app.php            应用配置  
│  ├─cache.php          缓存配置  
│  ├─cookie.php         Cookie配置  
│  ├─database.php       数据库配置  
│  ├─log.php            日志配置  
│  ├─session.php        Session配置  
│  ├─template.php       模板引擎配置  
│  └─trace.php          Trace配置  
│  
├─route                 路由定义目录  
│  ├─route.php          路由定义  
│  └─...                更多  
│  
├─public                WEB目录（对外访问目录）  
│  ├─index.php          入口文件  
│  ├─router.php         快速测试文件  
│  └─.htaccess          用于apache的重写  
│  
├─thinkphp              框架系统目录  
│  ├─lang               语言文件目录  
│  ├─library            框架类库目录  
│  │  ├─think           Think类库包目录  
│  │  └─traits          系统Trait目录  
│  │  
│  ├─tpl                系统模板目录  
│  ├─base.php           基础定义文件  
│  ├─convention.php     框架惯例配置文件  
│  ├─helper.php         助手函数文件  
│  └─logo.png           框架LOGO文件  
│  
├─extend                扩展类库目录    
├─runtime               应用的运行时目录（可写，可定制）  
├─vendor                第三方类库目录（Composer依赖库）  
├─build.php             自动生成定义文件（参考）  
├─composer.json         composer 定义文件  
├─LICENSE.txt           授权说明文件  
├─README.md             README 文件  
├─think                 命令行入口文件
## 函数库
目前用到的函数库统一存放在application/common.php下,后续单个模块需要用到可在模块文件夹下写入common.php
# 二次开发
## 数据库
## 系统功能

### 登录

* 管理员登录、记住密码、验证码刷新

### 首页

* 首页右上角管理员可以切换身份或者退出系统
* 首页主题色切换和清除缓存
* 首页顶部搜索菜单
* 首页顶部非admin模块作下拉菜单显示

### 管理员管理

* 管理员列表可以多选权限分组
* 管理权限支持菜单图标选择，绑定权限节点，和菜单显示配置
* 管理分组支持多级权限树选择
* 管理日志

### 用户管理

* 注册配置和注册开关
* 用户管理
* 用户角色
* 用户权限
* 用户日志

### 运营

* 广告位置和广告管理
* 积分管理

### 安全

* 行为限制

## 其他
