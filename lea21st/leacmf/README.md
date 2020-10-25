leacmf是一款基于ThinkPHP5.1+layui的极速App后台开发框架。
===============

**<label style="color:red">由于时间原因，该项目一直没有开发完成，bug非常多，很多都是半成品，建议大家暂时不要使用该项目，我会在最近发布一版全新的版本，并添加很多必备的功能,并且这一版有专门的人测试哈</label>字体**

laravel版本进 https://github.com/lea21st/leacmf-laravel

thinkphp5.0版进 https://github.com/lea21st/leacmf-thinkphp/tree/2.1

adminlte+layui2.2版进 https://github.com/lea21st/leacmf-thinkphp/tree/2.1

## **主要特性**

* 基于`RBAC(auth)`验证的权限管理系统
* API快速开发，已完成初始化程序
* 完善的前端功能组件开发
    * 基于`layui`开发
    * 封装了部分方法，开发快速简单，没有文档，看例子
 * 自动高亮菜单，自动面包屑，根据权限自动生成菜单树
 * 集成api验证,开发api和后台都快速方便
 * 在3.0版本的基础上，加入中间件的支持。取消在钩子里认证用户，放到中间件里了。
  
  
  ## **安装方式**  
  
leacmf 需要 PHP &gt;= 7.1以上的版本，并且同时需要PHP安装以下扩展

```
- cURL extension

- mbstring

- BC Math
```
使用 ` git ` 将代码clone到本地，导入数据库文件 `/data/backup/`下最新的数据库文件，修改`.env.example` 为 `.env`并在`.env`中配置好数据库和redis等。然后运行

```
composer update
```

## **在线演示**
/admin.php

用户名：admin
密　码：123456

## **二次开发（暂时随便写下，文档继续完善）**

#### 认证都放在行为里面，`tags.php`查看；

* 后台权限认证方法
* * `app()->rbac`获取权限对象，`app()->rbac->isRole`('super admin|admin')验证是否是super admin或者admin角色，
* * 获取当前登录的用户信息：`app()->rbac->user();`或者`app()->rbac->user;`或者app()->user;
* 调用上传图片` {:widget('upload/uoload',['field'=>'cover','type'=>'image','value'=>(isset($info['cover'])?$info['cover']:'')])}`，有图片时自动显示图片。字段名带[]这种字段将可以上传多张
* 调用富文本编辑器使用`{:widget('upload/editor')}`。
 * api认证
 * * 获取当前用户`app()->user`或者`app()->auth->user`;
 

* 更多功能暂时看源码吧，太忙，正式版未出之前不要用于正式环境，默认使用redis作为默认缓存，如未使用redis，在配置缓存中改成其它


## **界面截图**
![1](/public/static/1.png "1")
![2](/public/static/2.png "2")
![3](/public/static/3.png "3")
![4](/public/static/4.png "4")
![5](/public/static/5.png "5")

## **特别鸣谢**

感谢以下的项目,排名不分先后

ThinkPHP：http://www.thinkphp.cn

layui：http://www.layui.com

