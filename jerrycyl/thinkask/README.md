 **欢迎star 或者fork 您的支持是我们持续的动力** 
--
项目地址：http://www.thinkask.cn
--
因为调试，人力等相关原因。安装暂时不支持install浏览器安装。首先请把根目录的sql文件thinkask.sql导入到数据库后，再更改配置文件进行安装，后面的SQL更新会以语句的方式更新。
## UPDATE
---
v1.2
1,增加了文章模块
2，增加了数据库的备份
3，新加了安装功能
4，新加了第三方登陆功能
优化：
1，采集的优化
2，问答评论的优化
3，个人中心的优化

---
---
为了您的问题能尽快解决和群主的身心健康,有bug请提交在issues中,切勿在Q群刷屏,提问时请带上您的运行环境以便问题能够快速解决!

需要伪静态支持,规则如下
---
环境要求:
1)PHP >= 5.4.0(注意：PHP5.4dev版本和PHP6均不支持)
2)PDO PHP Extension
3)MBstring PHP Extension
4)CURL PHP Extension
5)开启静态重写(方法参考:http://www.kancloud.cn/manual/thinkphp5/177576)
6)要求环境支持pathinfo

---
 **相关图片：** 
后台重构，后面补上图片

---


### 简介

thinkask 是一个基于php7开发、tp5框架、免费开源的，问答管理系统


### 安全性
 XSS安全防护
 表单自动验证
 强制数据类型转换
 输入数据过滤
 表单令牌验证
 防SQL注入
 图像上传检测
 **运行环境** 
  PHP >= 5.4.0 （完美支持PHP7）
  PDO PHP ExtensionMBstring 
  PHP ExtensionCURL 
  PHP Extension

### DocumentRoot指向 
thinkask/public所在目录
---
### URL重写:

apache 重写：
```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```
phpstudy重写：
```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
</IfModule>
```
---
### 语言
支持多语言扩展，更好，更方便的成为国际化的问答系统


### 开源协议
遵循Apache2开源协议发布，Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。

### 交流群：   485114585
### 安装说明 

因为调试，人力等相关原因。安装暂时不支持install浏览器安装。首先请把根目录的sql文件导入到数据库后，再更改配置文件进行安装。


---



