prototype 1.0
===============


prototype，是一个后台原型系统，主要是解决开发中基础系统方案，基于onethink重构（与onthink的功能大致相同）,使用ThinkPHP 5.0.4开发,去除了独立模型和插件机制，保留Install安装模块。

###特点：
* 没有继承 controller 
* ajax提交返回和分页 
* html5 ajax异步上传文件 图片 裁剪功能
* 没有使用助手函数. 
* 去除独立模型和插件功能（都玩composer，还要插件干嘛）
* 使用php7新特性

prototype 预览地址 [prototype后台原型系统](http://tp5.calm7.com/admin/login/index.html)

ThinkPHP5详细开发文档参考 [ThinkPHP5完全开发手册](http://www.kancloud.cn/manual/thinkphp5)

运行环境 
===============

> prototype的运行环境要求PHP7.0.0以上。

> 建议通过虚拟域名访问

> mysql 建议关闭严格模式
 
window系统 WampServer Version 3.0.6 64bit 配置示例

配置如下通过虚拟域名访问

配置apache下的httpd-vhosts.conf文件 路径X:\wamp64\bin\apache\apache2.4.23\conf\extra

增加以下代码
~~~
<VirtualHost *:80>
    DocumentRoot "X:/xxx/tp5/public/"
    ServerName www.tp5.com
    ErrorLog "logs/dummy-host.example.com-error.log"
    CustomLog "logs/dummy-host.example.com-access.log" common
    <Directory "X:/xxx/tp5/public/">
    Options +Indexes +FollowSymLinks +MultiViews
    AllowOverride all
    Require all granted
</Directory>
</VirtualHost>
~~~
然后重启wamp

再打开自己本地的C:\Windows\System32\drivers\etchosts文件,配置如下：
~~~
127.0.0.1 www.tp5.com
~~~

注意：安装wamp好后，一定要切换到php7版本  部分css js 使用了Bootstrap中文网开源项目免费 CDN 服务 请务必联网

关闭mysql 的严格模式

修改my.ini

sql-mode="STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"

修改为

sql-mode="NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"


## 声明
此系统如应用于商业 请自行评估并承担相应责任
