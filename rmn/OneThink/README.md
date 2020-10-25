# OneThink（V1.0正式版）

## 前言

感谢大家使用OneThink！OneThink对我来说是一个比ThinkPHP更有意义的产品，因为她能让开发者和最终用户都能受益。作为一个开源产品，希望大家都能参与进来为OneThink添砖加瓦，OneThink团队一直都在致力于让OneThink更加优秀。现在，感谢您也参与其中。

## OneThink简介

OneThink是一套简单，易用，面向开发者的内容管理框架(CMF),帮助开发者节约web应用后台开发时间和精力，以最快的速度开发出高质量的web应用。

## 优势

### 基于ThinkPHP3.2

    依托新版ThinkPHP的功能优势和命名空间的特性，官方七年磨一剑，用心打造。更高，更快，更强！

### 安全可靠

    提供的稳健的安全策略，包括备份恢复，容错，防止恶意攻击登陆，网页防篡改等多项安全管理功能，保证系统安全，可靠，稳定的运行。

### 开源免费

    代码遵循Apache2开源协议，并且免费使用，对商业用户友好。OneThink将成为继ThinkPHP之后，另一面国产开源旗舰产品。

### 应用仓库

    官方应用仓库拥有大量的第三方的插件和应用模块、模板主题，众多来自开源社区的贡献，让你的网站“One”美无缺！

### 模块化开发

    全新的架构和模块化的开发机制，便于灵活扩展和二次开发。

### 用户行为

    支持自定义用户行为，可以对单个用户或群体用户的行为进行记录及分享，为您的运营决策提供有效参考数据。

### 文档模型/分类体系

    通过和文档模型绑定，以及不同的文档类型，不同分类可以实现差异化的功能，轻松实现诸如资讯，下载，讨论和图片等功能。

## OneThink目录说明

根目录
├─index.php     OneThink入口文件
├─Addons 插件目录
├─Application 应用模块目录
│  ├─Admin 后台模块
│  │  ├─Conf 后台配置文件目录
│  │  ├─Common 后台函数公共目录
│  │  ├─Controller 后台控制器目录
│  │  ├─Model 后台模型目录
│  │  ├─Logic 后台模型逻辑目录
│  │  └─View 后台视图文件目录 
│  ├─Common 公共模块目录（不能直接访问）
│  │  ├─Conf 公共配置文件目录
│  │  ├─Common 公共函数文件目录
│  │  ├─Controller 模块访问控制器目录
│  │  └─Model 公共模型目录
│  ├─Home Home 前台模块
│  │  ├─Conf 前台配置文件目录
│  │  ├─Common 前台函数公共目录
│  │  ├─Controller 前台控制器目录
│  │  ├─Model 前台模型目录
│  │  └─View 模块视图文件目录
│  └─User 用户模块（不能直接访问）
│     ├─Api 用户接口文件目录
│     ├─Conf 用户配置目录
│     ├─Common 后台函数公共目录
│     ├─Model 用户模型目录
│     └─Service 用户Service文件目录
├─Public 应用资源文件目录
│  ├─Admin 后台资源文件目录
│  │  ├─css 样式文件目录
│  │  ├─images 图片文件目录
│  │  └─js 脚本文件目录
│  ├─Home 前台资源文件目录
│  │  ├─css 样式文件目录
│  │  ├─images 图片文件目录
│  │  └─js 脚本文件目录
│  └─static 公共资源文件目录
├─Runtime 应用运行时目录
├─ThinkPHP 框架目录
└─Uploads 上传根目录
  ├─Download 文件上传目录
  ├─Picture 图片上传目录
  └─Editor 编辑器图片上传目录

## 安装

1、将OneThink压缩包解压至一个空文件夹，并上传它。
2、首次在浏览器中访问index.php将会进入安装向导。
3、按照安装向导完成安装。

## 最低系统需求

1、5.3.0 或 更高版本。
2、MySQL5.0 或更高版本。若在安装过程中出现问题，请访问[官网讨论区](http://www.onethink.cn/forum.html)寻求帮助。

## 系统推荐

启用mod_rewrite这一Apache模块。
在您的站点设置至 http://www.onethink.cn 的链接。

## 最后

对 OneThink有任何建议、想法、评论或发现了bug，请到[官网讨论区](http://www.onethink.cn/forum.html)
官方的[应用仓库](http://www.onethink.cn/store.html)包含了官方和第三方的各类插件。

## 分享精神

非常感谢您的支持！如果您喜欢OneThink，请将它介绍给自己的朋友，或者帮助他人安装一个OneThink，又或者写一篇赞扬我们的文章。
OneThink是对刘晨创建的[ThinkPHP](http://thinkphp.cn/)的传承和新的传奇。如果您愿意支持我们的工作，欢迎您对OneThink进行[捐赠](http://thinkphp.cn/donate/)。