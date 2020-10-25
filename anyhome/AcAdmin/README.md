### ApiCloud云端管理平台
标签： ApiCloud AcAdmin

----
> 在线演示
[http://acadmin.weiduzi.com](http://acadmin.weiduzi.com)
登录帐号 admin  登录密码 123456

####功能介绍    
1.实现通用统一的后台数据管理，查询、新增、修改、删除。    
2.实现ApiCloud的文件上传、删除管理。    
3.后台登陆用户管理    
4.APP应用管理
5.APP用户管理    
6.新增数据时候，实现图片上传功能（ApiCloud的file字段） 
7.实现Pointer字段的关联选择，

####支持
QQ交流群：196578969    
支持我：ayhome@gmail.com <支付宝>    
####使用场景
[ApiCloud](http://www.apicloud.com/) 是一个免费的在线生成混合型APP的平台，ApiCloud完全免费。    
在使用ApiCloud的过程中，不可避免的需要和维护后台数据库，虽然ApiCloud提供后台数据库服务---**数据云**，但并没有提供一个可视化的类似网站后台一样的操作平台，这对维护app后台数据带来极大不便。好在官方开发了数据云的API接口，AcAdmin便由此而生。    
**AcAdmin** 即 ApiCloud Admin的意思。    
使用AcAdmin可以无缝对接你的ApiCloud数据云，并想管理网站一样容易管理数据云

####运行环境
AcAdmin开源托管在开源中国，[点击下载](http://git.oschina.net/anyhome/AcAdmin)
ApiCloud基于[ThinkPHP 3.2.3]开发，后台使用了基于Bootstrap 的 admin框架，并且定制了其他组件，具体请参考应用资源。    
开发环境如下：
OS X 10.10.3、apache 2.2 + 、php 5.3+，windows环境没测试过。

####安装说明

 1. 安装
AcAdmin无须后台数据库支持，将下载的安装包解药到网站根目录，当然也支持二级目录。
 2. 修改配置文件
 使用notepad++、sublime text 等之类的文本编辑器打开配置文件 。
> Apps/Admin/Conf/config.php

修改登录密码，等着帐号统一为 admin

####使用帮助
1.使用配置文件的登录密码登录后台
2.新增应用，应用信息与ApiCould的保持一致。
3.新增之后刷新下当前页面，进入应用管理，
4.新增模型，根据提示进行操作即可。
5.根据实际情况设计表单

####引用资源
 - jquery
 - jquery.bsgrid
 - bootstrap
 - notification
 - nprogress
 - jquery.bootstrap-growl
 - bootstrap-confirmation

####预览
![登录界面](https://static.oschina.net/uploads/img/201510/22122057_KINB.png "在这里输入图片标题")
![输入图片说明](https://static.oschina.net/uploads/img/201510/22122118_AVRs.png "在这里输入图片标题")
![输入图片说明](https://static.oschina.net/uploads/img/201510/22122130_hmRS.png "在这里输入图片标题")
![输入图片说明](https://static.oschina.net/uploads/img/201510/22122139_gXwR.png "在这里输入图片标题")
![输入图片说明](https://static.oschina.net/uploads/img/201510/22122148_87nN.png "在这里输入图片标题")
![输入图片说明](https://static.oschina.net/uploads/img/201510/22122156_iHwa.png "在这里输入图片标题")