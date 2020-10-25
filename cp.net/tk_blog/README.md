# Cocolait博客

## 链接
- 博客：http://www.mgchen.com  
- github：https://github.com/cocolait 
- gitee：https://gitee.com/cocolait

## 简介
项目完全基于Thinkphp 3.2.3框架开发
![输入图片说明](http://git.oschina.net/uploads/images/2016/1125/175219_db5f2b9a_588912.png "cocolait博客")


## 使用说明
1.配置数据库,配置文件在/APP/Common/database.php 把该填的填好即可
2.数据库文件 在/sql/SQL/blog.sql 导入即可

## 项目介绍

1. 前端基于boostrap的响应式页面布局；
1. 前端css样式,采用的是Less自动生成；
1. 带表情的ajax无限级评论系统；
1. PHPMail邮件系统；
1. QQ、微博、微信等第三方登陆；
1. ueditor 百度富文本编辑器；
1. layer弹出层控件；
1. Share.js 分享控件,分享到微信,微博,QQ空间等等...；
1. 引入了animate.css(动画) 和 csshake.css(晃动)；
1. ajax异步轮训,用于消息推送;
1. 基于TP3.2 auth权限控制；
1. webuploader 上传图片；
1. 项目支持Composer安装依赖第三方库；

## php版本要求
>php > 5.5

## 问题说明
1. 服务找不到一般都是url不支持pathinfo模式，因为我默认配置的兼容模式，所以必须支持pathinfo模式。
2. 验证码无法正常显示，请确保你的php是否安装GD库，可phpinfo() 进行搜索查看

## 其他说明
> 该项目将不在更新,新版博客 mgchen.com 已基于tp5架构重新打造。

## 如何支持pathinfo
1. 如果是apache服务器请确保以下操作是否启用：
```php
1、httpd.conf配置文件中加载了mod_rewrite.so模块
2、AllowOverride None 将None改为 All
```

2.如果是nginx,在Nginx低版本中，是不支持PATHINFO的，但是可以通过在Nginx.conf中配置转发规则实现：
```php
location / {
    if (!-e $request_filename) {
        rewrite ^/(.*)$ /index.php?s=/$1 last;
    }
}
```

## 后台
后台登录,直接输入http://serverName/admin 就可进入
默认登录名 admin 密码 admin