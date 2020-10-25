# PhalApi 2.x 版本完美诠释

## 2.x 版本系统架构

PhalApi 2.x 版本的系统架构如下：  

![](http://cdn7.phalapi.net/20170708092204_54812b18c33ab263331685a5a7c18400)

主要分为三层：  

 + **phalapi/phalapi**  项目应用层，可使用phalapi/phalapi搭建微服务、接口系统、RESTful、WebServices等。  

 + **扩展类库**  扩展类库是指可选的、可重用的组件或类库，可以直接集成使用，由广大开发人员维护分享，对应原来的PhalApi-Library项目。  

 + **核心框架**  分别两大部分，PhalApi核心部分kernal，以及优化后的notorm。  

其中，各自的composer和github项目分别是：  

项目|composer|github
---|---|---
phalapi/phalapi|[phalapi/phalapi](https://packagist.org/packages/phalapi/phalapi)|[phalapi/phalapi](https://github.com/phalapi/phalapi)
扩展类库|由广大开发人员共同维护、分享，composer建议统一注册到[phalapi](https://packagist.org/packages/phalapi/)。|由广大开发人员共同维护、分享，源代码可维护在开发者各自的Github仓库。
核心框架|[phalapi/kernal](https://packagist.org/packages/phalapi/kernal)|[phalapi/kernal](https://github.com/phalapi/kernal)

## 框架核心部分UML静态结构图

PhalApi 2.x 版本的核心框架部分的UML静态结构图，高清版如下所示：  

![](http://cdn7.phalapi.net/20170709202718_71d58eb802f21c8267f413227cf8aa3e)

首先，绿色部分的PhalApi\PhalApi类是整个接口系统的访问入口，也就是项目应用系统、客户端使用的关键所在。相关的调用代码，可以参考统一入口文件的实现代码片段。 
```php
$pai = new \PhalApi\PhalApi();
$pai->response()->output();
```
只需要两行代码，便可完成对接口服务的请求响应。  

其次，是黄色部分的Api、Domain和Model这三层，也就是我们常说的ADM分层架构。这部分，需要开发人员关注，因为这也是具体项目开发需要自己实现的部分。  

最后，是红色部分的DI依赖注入，也是整个框架的核心所在。不仅在核心框架中使用频率最高，乃至在项目应用中也会经常被用到。

## 核心执行流程时序图

PhalApi 2.x 版本的核心执行流程时序图，与1.x 版本基本一致，可以看出，不管技术如何升级，PhalApi的最初的核心时序流程仍保持着活力。唯一变化的是各个类名。  

![](http://cdn7.phalapi.net/20170709205008_e0e6c19762f98188085e9e42ce944cb3)
