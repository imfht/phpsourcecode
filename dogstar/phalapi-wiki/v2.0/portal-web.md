# 运营平台前端部分

运营平台前端使用的是[layui](https://www.layui.com/)经典模块化前端框架和[layuimin](http://layuimini.99php.cn/)LAYUI MINI 后台管理模板的【iframe版 - v2】进行开发，
开发过程中，关于前端部分可参考layui的开发文档。 

## PhalApi运营平台主要界面

目前，主要界面有：  

 + 运营平台首页：/portal/
 + 安装界面：/portal/page/install.html
 + 登录界面：/portal/page/login-1.html
 + 菜单管理界面：/portal/index.html#/page/menu.html

可以根据项目的情况，进行调整。

## 运营平台链接写法

URL链接写法，可以有三种方式，分别是：  
 + 第1种：完整的URL链接，例如：http://mac.phalapi.net/portal/index.html（前面使用http或https协议）
 + 第2种：相对于根路径的写法，例如：/portal/index.html（前面有斜杠）
 + 第3种：相对于当前文件的写法，例如：page/menu.html（前面没有斜杠）

这里遵循layuimini原来的写法，在模板中或者在菜单切尔西中推荐使用第3种写法，相对于当前文件的写法。  

这样的好处在于，当调整portal目录的位置时，影响较少，并且当域名发生变化时也不受影响。  

## 在运营平台前端需要调用的API接口

在运营平台的前端，需要调用后端的API接口时，需要注意以下几点。  

 + 第1点：页面初始化时需要调用```/?s=Portal.Page.StartUp```初始化接口，如果未登录则跳转到登录页面
 + 第2点：调用接口时，统一使用相对于根路径的写法，即：```/?s=接口服务名称```
 + 第3点：调用的接口，都应用放置在Portal大分类下，对应在线接口文档的运营平台。如下图所示。  

![](http://cdn7.okayapi.com/yesyesapi_20200309213546_4a69cc347feddc5e25e90e4c6180de61.png)

