# 基于阿里云安全接口的内容安全微服务

## 概述

这是一个基于阿里云安全接口而编写的内容安全微服务，主要是为内网提供持续的内容检查和发现机制。

架构如下：

![“应用-微服务-阿里云安全”微服务通讯架构](http://aliyun-microsrv-demo.cn-hangzhou.aliapp.com/static/introapp/img/csc-aliyun-microsrv-ugc-app-microsrv-connect.png)

本微服务同时也是以下demo：

* 淘宝OAuth 2.0登录demo
* “阿里巴巴SDK For PHP”demo

## 演示地址

该demo放置在阿里云ACE app容器中。访问地址：[http://aliyun-microsrv-demo.cn-hangzhou.aliapp.com/](http://aliyun-microsrv-demo.cn-hangzhou.aliapp.com/)

本演示网站将于2016年03月30日到期。

## 特性

* 工业实际可用（部署在内网）
* 符合PSR-4载入方式，预留Composer部署

## 系统要求

* PHP 5.4或以上
* PHP启用Curl扩展、且安装了OpenSSL（因为要调用HTTPS）

## 文档

* [首次安装和使用方法](http://git.oschina.net/horseluke/content-guard-microsrv-aliyun/blob/master/doc/Install.md)
* [基于阿里云安全接口的内容安全微服务API接口说明和列表](http://git.oschina.net/horseluke/content-guard-microsrv-aliyun/blob/master/doc/MicroServiceApi/Intro.md)
* [Faq](http://git.oschina.net/horseluke/content-guard-microsrv-aliyun/blob/master/doc/Faq.md)

## 其它

如果存在使用上的问题、发现bug或者想提建议，请在此发issue、或发邮件到horseluke@126.com。

如果发现安全漏洞，请直接发邮件到horseluke@126.com。

如需相关服务的企业定制或企业应用集成，请发邮件到horseluke@126.com。

以上反馈信息本人会详细评估，并进行适合的沟通和处理。

![觉得好用，用支付宝打赏给作者攒钱买房](http://7xlz3z.com1.z0.glb.clouddn.com/img/git/apavqmok7au6ummhae.png)


## 参赛说明

本作品为2015"云朵之上，编码未来"[阿里云开源编程马拉松](http://bbs.aliyun.com/read/256663.html?spm=5176.100131.1.6.urYu37)参赛作品之一。[根据比赛规则](http://www.oschina.net/2015-ali-hackathon#item-rule)，本作品托管在[Git@OSC](http://git.oschina.net/)上。

本作品属于项目选题“基于阿里云安全接口的内容安全微服务”的作品系列之二：内容安全微服务（及demo）。该作品定位为内网的内容安全微服务，应用在传媒界上，是对“阿里巴巴SDK”（作品一）的实际工程运用能力证明。

本选题作品系列之一，即“阿里巴巴SDK”开源参赛作品，请访问[horseluke / AlibabaSDK](http://git.oschina.net/horseluke/AlibabaSDK)。

## 协议

Apache License, Version 2.0

```

Copyright 2015 Horse Luke

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

```

