#Uyghur Blog Project !
---
QeyserBlog 博客系统是基于ThinkPHP5核心，PHP版本要求提升到5.4以上，是在作者凯萨尔（Qeyser.Mutellip）的坚持不懈努力下，为广大维吾尔PHP学者开源和免费提供使用，帮助开发人员更加方便的进行WEB应用的快速开发。

===============

> 更新日志： 

2018年4月1日  更新到 v1.7 版本 （内核TP5.0.16）  
-升级内核

2017年7月24日 更新到 v1.6 版本 （内核TP5.0.10）  
-添加主题风格后台管理功能  
-重构模板目录结果

2017年7月9日  更新到 v1.5 版本 （内核TP5.0.10）  
-升级内核
 
> QeyserBlog 的运行环境要求PHP5.4以上，  
安装地址：http://url/index.php/install

官网地址：http://www.qeyser.net
详细开发文档参考 [QeyserBlog开发手册](http://doc.qeyser.net/)
Git下载地址：http://git.oschina.net/qeyser/qeyserblog

## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application      应用目录（可设置）
│ ├─admin          后台模块目录（可更改）
│ ├─index          前台模块目录(可更改)
│ ├─install        安装模块目录(可更改)
│ ├─command.php    命令行工具配置文件
│ ├─common.php     应用公共（函数）文件
│ ├─config.php     应用（公共）配置文件
│ ├─database.php   数据库配置文件
│ ├─tags.php       应用行为扩展定义文件
│ └─route.php      路由配置文件
├─extend           扩展类库目录（可定义）
├─public           WEB 部署目录（对外访问目录）
│ ├─static         静态资源存放目录(css,js,image)
│ ├─ueditor        百度编辑器目录
│ ├─tpl            自定义模板文件目录
│ └─data           文件目录
├─runtime          应用的运行时目录（可写，可设置）
├─vendor           第三方类库目录（Composer）
├─thinkphp         框架系统目录
│ ├─lang           语言包目录
│ ├─library        框架核心类库目录
│ ├─tpl            系统模板目录
│ └─start.php      框架引导文件
├─build.php       自动生成定义文件（参考）
├─composer.json    composer 定义文件
├─think            命令行入口文件
│─LICENSE          授权说明文件
│─README.md        README 文件
│─.htaccess        用于 apache 的重写
│─favicon.png      icon 文件
│─index.php        入口文件
│─UKIJEkran.eot    Eot 文件
│─UKIJEkran.svg
│─UKIJEkran.ttf
│─UKIJEkran.woff
~~~

## 版权信息

QeyserBlog遵循Apache2开源协议发布，并提供免费使用。

版权所有Copyright © 2017-2018 by Qeyser.Mutellip (http://www.qeyser.net)

All rights reserved。
