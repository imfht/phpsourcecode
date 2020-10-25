# PhalApi 2.x 接口开发 - 如何请求接口服务

## 视频
[第四课 PhalApi 2.x 接口开发 - 如何请求接口服务](https://www.bilibili.com/video/av83734878)

[![](http://cdn7.okayapi.com/yesyesapi_20200118215334_a04c5c973772b2a4c7e2519f33d35ee5.png)](https://www.bilibili.com/video/av83734878)

## 访问入口
+ 第一种方式：域名
+ 第二种方式：域名/public/

> 经验法则：网站根目录通常建议设置在public目录或者指定子目录，设置为根目录会存在安全性问题。

## 如何指定待请求的接口服务？
+ 默认接口服务：App.Site.Index
+ 默认命名空间：App
+ 接口格式：Namespace.Class.Action
+ 一些示例（App\Api\Hello::world()），命名空间为App，类名为Hello，方法名是wolrd，组合起来接口服务名称是：App.Hello.World

## 开启URI路由匹配
+ 效果
+ 配置
+ 示例

> 穿插：PhalApi名称怎么来的？ 有一个知名的开源PHP框架，叫Phalcon

## 扩展：如何定制接口服务的传递方式？
+ 代码实现
+ 配置
+ 示例


