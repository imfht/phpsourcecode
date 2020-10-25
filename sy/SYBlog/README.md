# SYBlog

泷涯开发的博客程序，基于SYFramework

SYBlog代码基于[GPL2.0](https://opensource.org/licenses/GPL-2.0)开源

**tool目录下为各种工具，不需要上传到网站中去**

**public/install目录下为安装程序，安装完请尽快删除**

# 环境需求

* PHP5.6及以上（较老的版本不保证完全兼容，推荐PHP7.0或更新版本）

* PDO扩展和PDO_Mysql驱动

* XMLWriter扩展

# 安装使用

### 全新安装

* 上传application目录、framerowk目录、public目录至网站

* 根据环境部署Rewrite目录下对应的重写规则

* 打开 http://您的网站/install （例如 http://example.com/install/ ）按照向导完成安装过程

* 删除public/install目录

### 重写说明

如果服务器不支持URL重写，请在安装完成后，修改`application/config.php`：

```
'rewrite' => [
	'enable' => TRUE, //将此处的TRUE改为FALSE
```

### 升级

**从2.0.0升级至2.1.0**

* 打开phpMyAdmin或类似软件，在安装SYBlog的数据库中，执行以下命令，其中，将`#@__`替换成你自己的数据表前缀，例如你的数据表前缀是`blog`，则替换后是`blog_option`：

```SQL
INSERT INTO `#@__option` VALUES ('apiKey','');
```

* 如果您不想重新修改config.php，您可以在旧配置文件的第35行处，将`'modules' => ['admin', 'index']`修改为`'modules' => ['admin', 'index', 'api']`。或者，您也可以修改新的配置文件后覆盖

* 上传新文件，覆盖旧文件

**从1.X升级至2.0.0**

2.0.0版本不兼容原有版本的文件结构。升级方式如下：

* 将`public/install/data/config.php`中的数据库配置和cookieKey替换为原站点的配置后，覆盖`application/config.php`

* 参照新的模板示例，改写模板

* 重新部署重写规则并将root目录指向public目录

数据库内容保持不变

# Android APP

说明和代码见[sy/SYBlog-Android](https://gitee.com/sy/SYBlog-Android)

# 进度

**2.1.2正式版**已发布

点击[这里](https://git.oschina.net/sy/SYBlog/tags)查看所有版本和下载

# Changelog

### 2.1.2

* 修复不兼容PHP7.2的问题

* 修复安装程序无法锁定的问题

### 2.1.1

* 修复几处BUG

### 2.1.0

* 增加了几个API

### 2.0.0

__只是增加了一点说明__

### 2.0.0候选版-2

* Fix.部分页面报错

* Fix.几处错误URL

### 2.0.0候选版-1

* 重构代码

**注意：此版本部分内容不向下兼容**

### 1.0.1

* Add.支持PHP7

### 1.0.0

* Add.真静态生成

* Add.附件库（包括附件管理和附件删除）

* Add.模板分页函数

* Add.Sitemap模板

* Fix.登录页面无法用回车键提交

* Fix.Help报错

* Fix.页码按钮对齐

* Fix.安装文件无法初始化管理员密码

* Opt.独立header和footer，方便修改静态文件地址

### Beta 0.4

* Add.附件编辑

* Add.登录页美化

* Add.附件大小设置

* Add.Sitemap生成

* Add.body高级输出

* Fix.修复编辑文章Bug

* Fix.修复百度主动推送

* Fix.附件上传无法正常处理服务器错误的问题

* Fix.其他杂杂的Bug

### Beta 0.3

* Add.界面风格改为MaterialDesign

* Add.阿里顽兔附件支持

* Add.安装程序

* Add.Ping和百度主动推送

* Add.附件上传

* Add.安装向导

* Fix.Feed多处Bug

* Fix.编辑器多处Bug

* Opt.编辑器自动保存

### Beta 0.2

* Add.Feed支持ATOM

* Add.远程附件支持（FTP,七牛,又拍,阿里OSS）和附件设置

* Fix.后台部分功能和按钮无效

* Fix.修复RDF时间格式错误

* Fix.分类（Tag）的URL重写