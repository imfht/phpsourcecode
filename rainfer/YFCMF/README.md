# YFCMF3.0.0 快速开发框架
[![YFCMF](https://img.shields.io/hexpm/l/plug.svg)](http://www.yfcmf.net/)
[![YFCMF](https://img.shields.io/badge/YFCMF-v3.0.0-brightgreen.svg)](http://www.yfcmf.net/)
[![star](https://gitee.com/rainfer/YFCMF/badge/star.svg?theme=dark)](https://gitee.com/rainfer/YFCMF/stargazers)
[![fork](https://gitee.com/rainfer/YFCMF/badge/fork.svg?theme=dark)](https://gitee.com/rainfer/YFCMF/members)


## 环境要求:
* PHP >= 5.6.0
* PDO PHP Extension
* MBstring PHP Extension
* CURL PHP Extension
* 开启静态重写(方法参考:https://www.kancloud.cn/manual/thinkphp5_1/353955)
* 要求环境支持pathinfo
* 要求安装Zip扩展(插件/模块市场需要)

### 官网
[http://www.yfcmf.net/](http://www.yfcmf.net/)
### 文档
[http://doc.yfcmf.net/](http://doc.yfcmf.net/)
### 交流
[http://sns.yfcmf.net/](https://sns.yfcmf.net/)
### 后台演示（用户名:admin 密码:123456）
- V3.0.0版本：[http://v3.yfcmf.net/admin/](http://v3.yfcmf.net/admin/)
- V2.3.1版本：[http://v2.yfcmf.net/admin/](http://v2.yfcmf.net/admin/)
## 重写设置
### [Apache]
httpd.conf配置文件中加载了mod_rewrite.so模块
AllowOverride None 将None改为 All
把下面的内容保存为.htaccess文件放到应用入口文件的同级目录下
 
```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>

```
如果为phpstudy

```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
</IfModule>
```
如果还是不行,请添加"?"

```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```
### [IIS]
如果你的服务器环境支持ISAPI_Rewrite的话，可以配置httpd.ini文件，添加下面的内容：
```
RewriteRule (.*)$ /index\.php\?s=$1 [I]
```
在IIS的高版本下面可以配置web.Config，在中间添加rewrite节点：

```
<rewrite>
 <rules>
 <rule name="OrgPage" stopProcessing="true">
 <match url="^(.*)$" />
 <conditions logicalGrouping="MatchAll">
 <add input="{HTTP_HOST}" pattern="^(.*)$" />
 <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
 <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
 </conditions>
 <action type="Rewrite" url="index.php/{R:1}" />
 </rule>
 </rules>
 </rewrite>
```
### [Nginx]
在Nginx低版本中，是不支持PATHINFO的，但是可以通过在Nginx.conf中配置转发规则实现：
```
location / { // …..省略部分代码
   if (!-e $request_filename) {
       rewrite  ^(.*)$  /index.php?s=/$1  last;
    }
}
```
其实内部是转发到了ThinkPHP提供的兼容URL，利用这种方式，可以解决其他不支持PATHINFO的WEB服务器环境。
如果你的应用安装在二级目录，Nginx的伪静态方法设置如下，其中youdomain是所在的目录名称。
```
location /youdomain/ {
    if (!-e $request_filename){
        rewrite  ^/youdomain/(.*)$  /youdomain/index.php?s=/$1  last;
    }
}
```

## [UPDATE]
V3.0.0

* 升级至最新ThinkPHP5.1.*为核心框架(跟随TP5.1.*更新而更新)
* 增加Widget表单构造器
* 重构编码规范


# 系统介绍

##各种设备自适应

- 响应式的网站设计能够对用户产生友好度，并且对于不同的分辨率能够灵活的进行操作应用。 简洁通俗表达就是页面宽度可以自适应屏幕大小，一个网站PC、手机、PAD通吃，页面地址一致。

- 一个字“酷“，可以用PC浏览器拉动窗口大小，网站内容显示依旧在设计之内，用户体验非常不错。 一个字“省”，一个网站PC、手机、PAD通吃，这样就不用花那么多心思去维护多个网站，无论是制作还是数据内容。


##基于HTML5技术

- HTML5对于用户来说，提高了用户体验，加强了视觉感受。HTML5技术在移动端，能够让应用程序回归到网页，并对网页的功能进行扩展，操作更加简单，用户体验更好。 

- HTML5技术跨平台，适配多终端。对于搜索引擎来说，HTML5新增的标签，使搜索引擎更加容易抓取和索引网页，从而驱动网站获得更多的点击流量。


##人性化的后台管理

- 传统的企业网站管理系统是以技术人员的角度出发，设计了很多复杂的功能，并且操作流程上也很复杂，对于最终要操控这个系统的管理员来说并不是很人性化，YFCMF所做的只是简化不必要的功能，从操作习惯下合理地布局和设计界面，让最普通的用户，即使没有网站管理的经营，也能很容易上手我们的系统。



# 许可协议

YFCMF企业系统遵循Apache2开源协议发布。Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。需要满足的条件:

1. 需要给代码的用户一份Apache Licence ；
2. 如果你修改了代码，需要在被修改的文件中说明；
3. 在延伸的代码中（修改和有源代码衍生的代码中）需要带有原来代码中的协议，商标，专利声明和其他原来作者规定需要包含的说明；
4. 如果再发布的产品中包含一个Notice文件，则在Notice文件中需要带有Apache Licence。你可以在Notice中增加自己的许可，但不可以表现为对Apache Licence构成更改。

具体的协议参考：[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)。