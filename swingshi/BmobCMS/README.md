#BmobCMS
BmobCMS 是基于[OneThink](http://www.onethink.cn)和[Bmob](http://www.bmob.cn)的项目；提供Bmob数据管理和权限分配，并可以定制界面UI，方便运营管理和维护。QQ群：460082321。
###OneThink
[OneThink](https://github.com/liu21st/onethink)是一个开源的内容管理框架，基于最新的ThinkPHP3.2版本开发，提供更方便、更安全的WEB应用开发体验，采用了全新的架构设计和命名空间机制，融合了模块化、驱动化和插件化的设计理念于一体，开启了国内WEB应用傻瓜式开发的新潮流。
###Bmob
国内首家专注于移动应用云服务的平台,baas模式的云服务平台,完全免费的云服务平台。
使用Bmob几乎能免去所有服务器端编码工作量，灵活简便的移动后端云服务，专注移动端音视频流处理服务，业内领先的云存储文件服务系统，最大限度的保障你云端数据的安全。

##BmobCMS出现目的？
* 通过自己的后台定制化管理、统计数据，方便用户操作；
* 通过Api实现数据操作，导入、导出、备份、审核、删除等；
* 后台可以根据不同用户分配不同app权限；

##BmobCMS计划
* 后台用户、Apps、数据库表权限；
* Bmob数据接入，并可以实现数据的CRUD；
* Bmob数据结构和OneThink后台模型同步；
* 数据采集和任务调度管理；
* 接入Bmob推送、支付、短信、聊天功能；

##期待您的加入##

##BmobCMS安装
0. 环境
>参考OneThink
1. 后台系统名称
>/Application/Admin/Conf/config.php,配置ADMIN_NAME
2. 后台系统名称logo
>/Application/Admin/Conf/config.php,配置ADMIN_TOP_NAME
3. logo：
>logo.png
4. 数据库
>/bmobcms.sql
5. 数据配置：/Application/User/Conf/config.php，/Application/Admin/Conf/config.php
>修改其中的：数据库名、用户名、密码
6. 后台登录名
>admin/123456

##BmobCMS进度
####后台用户、Apps、数据库表权限；
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/2.png)
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/1.png)
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/3.png)
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/4.png)
####Bmob数据接入，并可以实现数据的CRUD；
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/9.png)
####Bmob数据结构和OneThink后台模型同步；
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/5.png)
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/6.png)
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/7.png)
![BmobCMS](http://git.oschina.net/swingshi/BmobCMS/raw/master/Screen/10.png)



