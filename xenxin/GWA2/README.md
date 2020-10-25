# GWA2
General Web Application Architecture.  通用網絡應用架構.

![gwa2](http://ufqi.com/blog/wp-content/uploads/2016/09/gwa2-logo-201606.v2.png)
----
[-NatureDNS](http://ufqi.com/naturedns) : [-GWA2](http://ufqi.com/naturedns/search?q=-gwa2) , [-吉娃兔](http://ufqi.com/naturedns/search?q=-吉娃兔)

* GWA2 speaks in Java ![](https://cn.bing.com/th?id=AMMS_3ab989ddcf0158808db9f2bcd199abc1&w=110&h=110&c=7&rs=1&qlt=80&pcl=f9f9f9&cdv=1&dpr=1.1&pid=16.1) , 

* GWA2 speaks in PHP ![](https://cn.bing.com/th?id=AMMS_22939a371373b00dadf52557666432cf&w=110&h=110&c=7&rs=1&qlt=80&pcl=f9f9f9&cdv=1&dpr=1.1&pid=16.1) , 

* GWA2 speaks in Perl ![](http://www.horter.de/i2c/i2c-beispiele/perl_logo2.gif) , 

* GWA2 speaks in Aspx ![](http://www.slashcoding.com/wp-content/uploads/2013/09/asp-net-logo.gif) , and 

* GWA2 speaks in Python ![Python](https://www.python.org/static/img/python-logo.png) .

.


### GWA2 is cross-scripts programming framework for web applications.


* From Interface to Interactions

* From Demand to Design

![gwa2-architecture](http://ufqi.com/dev/gwa2/general.webapp.arch-201107.png)

***Three Layers***
----
*Core Services*, 
  which also being called compenents, consists of databases, sessions, quenes, caches, file-systems....; 

*Objects*, 
  business-oriented objects, which stand for what to do, i.e., users, products, transactions, locations, articles....; 

*Interactions*, 
  beared with objects, which define how to do, i.e., request, responses, UI, UE, HCI.... 

***Three Layers II***
----
*Modules* , 

*Controllers* ,

*Views*

***Three Layers III***
----
*Objects* , 

*Handlers* ,

*Drivers*

***Three More Instances***
----
*-PHP* Version  

*-Java* Version

*-Aspx* Version

*-Perl* Version

*-Python* Version



----

***[GWA2 Updates](http://ufqi.com/blog/category/computer-tech/%E9%80%9A%E7%94%A8%E7%BD%91%E7%BB%9C%E5%BA%94%E7%94%A8%E6%9E%B6%E6%9E%84/)***

GWA2写日志的两种方法setBy和debug

-GWA2 Java版本的i18n/中文编码/乱码问题

-GWA2新增命令行运行及NO_SQL_CHECK支持

gwa2更新多数据库驱动支持

-gwa2 vs -thinkphp

-GWA2更新：扩展增加read/writeObject to setBy/getBy

-gMIS/-GWA2更新：自动安装程序和MySQLi

-GWA2 core updates: 容错处理,孤儿占位符

-gwa2 安全更新及域名绑定到模块

-gwa2安全更新

-gMIS更新兼容Strict SQL Mode



----

# An Example Functional Module (User)

## User module, 用户模块

### 0. Create data table, 建用戶表

```sql
create table gmis_usertbl(
  id int(11) not null auto_increment,
  iname char(32) not null default '',
  email char(32) not null default '',
  password char(64) not null default '',
  ibirthday date not null default '0000-00-00',
  igroup tinyint(1) not null default 0,
  istate tinyint(1) not null default 1,
  inserttime datetime not null default '0000-00-00 00:00:00',
  updatetime datetime not null default '0000-00-00 00:00:00',
  primary key k1(id),
  unique index k2(email)
);
```

### 1. Create module class, 创建对象类
mod/user.class.php

继承  inc/webapp.class.php 

实现   inc/webapp.interface.php 


### 2. Create controller, 创建控制器
ctrl/user.php

引入 mod/user.class 并实例化

根据 $act 区分不同的代码模块

  act=signin

  act=signup

  act=update

  act=logout

  act=my

在控制器中处理逻辑，然后指定要加载的模板，引入 smarty （自2019年起，模板引擎改为 [Hanjst/汉吉斯特](https://ufqi.com/dev/hanjst/)）


### 3. Create view, 创建视图页面
view/default/signin.html

其中 view 是模块目录， default 是默认风格

  view/default/signup.html

  view/default/update.html

  view/default/logout.html

  view/default/my.html
  

### 4. Define routing, 在入口定义路由
/index.php?mod=user&act=xxxx

.


# Installation for GWA2 in PHP

Please put all files under a sub directory and init from the script ./install.php .

For GWA2 in Java and other languages versions, manual work is still in need.





