# 第二课 PhalApi 2.x 接口开发 - 下载和安装

## B站视频链接
[第二课 PhalApi 2.x 接口开发 - 下载和安装](https://www.bilibili.com/video/av83160783/?redirectFrom=h5)

[![](http://cdn7.okayapi.com/yesyesapi_20200113095442_2f39af463eac2f457c5dbeb3f5ae3d60.png)](https://www.bilibili.com/video/av83160783/?redirectFrom=h5)

## 下载方式
 + 直接下载压缩包
 + 通过git命令clone仓库
 + 通过composer命令创建接口项目
 + 内部传代码（推荐使用最新版本的PhalApi框架）

## 部署方式
本地环境：
 + Windows系统，xampp集成环境
 + Mac系统，Nginx
 + 虚拟机+Ubuntu

测试\线上环境： 
 + Linux/CentOS系统，Nginx
 + 宝塔，Nginx

前置条件：系统运行环境已经安装搭建完毕。即：
 + Nginx/Apache已安装成功并已启动
 + MySQL已安装并已启动
 + PHP已安装并已启动php-fpm

## Windows系统，xampp集成环境
Zip压缩包下载
todo, xampp，如何配置本地域名

```
<VirtualHost *:80>
DocumentRoot "D:\xampp\htdocs\PhalApi\public"
ServerName api.demo.com
ErrorLog "logs/api.demo.com-error.log"
CustomLog "logs/api.demo.com-access.log" common
</VirtualHost>
```

假设配置域名为：api.demo.com  
需要添加的本地host为：127.0.0.1 api.demo.com  

通过 http://api.demo.com 访问默认接口服务  
通过 http://api.demo.com/docs.php 访问在线接口文档  

## Mac系统，Nginx
Git命令进行clone来下载，github稍慢一点，码云较快了（瞬间下载完毕）。
进行nginx配置，

假设配置域名为：api.demo.com   
需要添加的本地host为：127.0.0.1 api.demo.com  

通过 http://api.demo.com 访问默认接口服务  
通过 http://api.demo.com/docs.php 访问在线接口文档  

## Linux系统，Nginx
暂时不重复讲解。

## 宝塔，Nginx
创建网站，上传解压PhalApi代码，设置public目录，配置hosts，重启apache/nginx

