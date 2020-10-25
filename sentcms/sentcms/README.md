## SentCMS介绍
> SentCMS网站管理系统是南昌腾速科技有限公司倾力打造的一款简单易用的网站管理系统，SentCMS网站管理系统（下文简称SentCMS）继承了thinkphp的优秀品质，秉承“大道至简”的设计理念。SnetCMS为网站建设而生，为网站建设减少90%的代码编写，只需前端设计师就可以设计出完美的网站，而如此完美的系统还是完全开源的。

## 环境要求

生产环境建议Linux+Nginx+php+mysql
建议PHP7+

## 在线预览

demo： http://www.sentcms.com/

账号：admin
密码：admin888

## 安装

为了保证系统的安全性，系统根目录移至public目录下，把程序和逻辑代码放到根目录以外。在配置网站时，把网站根目录指向web目录下，然后通过composer把所需的扩展类库更新，包括thinkphp框架同样通过composer进行更新。

> 最好是使用云主机进行安装，虚拟主机有很多局限性

```
git clone https://gitee.com/sentcms/sentcms.git sentcms
cd sentcms
```
> 如果是直接下载压缩包则直接进入解压文件夹，然后执行下面的代码

```
composer install
```

访问网址：http://网址/install.php

> 系统必须开启伪静态、
> 系统必须开启伪静态、
> 系统必须开启伪静态、
>（重要的事情说三篇）

## 交流讨论

>  * 点击链接加入2群【sentcms网站管理系统2群】：https://jq.qq.com/?_wv=1027&k=5ewmZ0w

>  * 点击链接加入1群【SentCMS交流群】：http://jq.qq.com/?_wv=1027&k=2DXSKpe（已满） 勿加


## SentCMS特性包括：
* 全新的路由体系，完美的路由解决方案
* 全新的系统架构，采用thinkphp6.0.* 内核框架
* 完善而健全的会员体系
* 健全的权限系统，权限细化到界面上的按钮和链接
* 漂亮的后台界面，后台界面采用世界领先的前端框架bootstrap，自适应的体验
* 简单易用的标签体系
* 便捷的文档系统，无需程序即可设计出完美的模型，做出完美的网站，传送门：http://www.kancloud.cn/tensent/sentcms4（未完成）

下载最新版框架后，解压缩到sentcms目录下面，可以看到初始的目录结构如下：
## 目录结构
~~~
├─addons                扩展插件目录
├─app                   项目目录文件
│ ├─controller          控制器文件
│ │ ├─admin               网站后台控制器文件夹
│ │ ├─api                 网站API控制器文件夹
│ │ ├─user                网站用户中心控制器文件夹
│ │ ├─front               网站前台控制器文件夹
│ │ ├─Base.php            网站控制器基类
│ │ ├─Upload.php          网站文件上传控制器
│ ├─http                HTTP服务
│ │ ├─form              表单控件文件夹
│ │ ├─middleware        中间件文件夹
│ │ ├─validate          验证类文件夹
│ ├─model               模型文件
│ │ ├─Member.php      用户模型
│ │ ├─ …………
│ ├─common.php        公共函数库文件
│ │ ├─ …………
├─runtime                缓存以及备份目录
├─extend              自定义类库扩展目录
├─vendor              网站扩展类库目录，通过composer更新下载的类库扩展在此目录
├─public              网站根目录
│ ├─static         静态文件文件夹
│ ├─uploads        上传文件目录
│ ├─template       网站主题模板目录
│ ├─.htaccess      Apache下伪静态文件
│ ├─favicon.ico    ico图标
│ ├─index.php      入口文件
├─.example.env        系统配置模板
├─composer.json       composer配置文件
├─README.md           系统介绍文件
~~~

## 版本列表

> 4.0  https://gitee.com/sentcms/sentcms

> 3.2： https://git.oschina.net/sentcms/sentcms/repository/archive/3.2

> 3.0.1804： https://git.oschina.net/sentcms/sentcms/repository/archive/3.0.1804

> 3.0.1707： https://git.oschina.net/sentcms/sentcms/repository/archive/3.0.1707

> 3.0.161201：https://git.oschina.net/sentcms/sentcms/repository/archive/3.0.161201