# liteadmin

这个项目的初心是给有开发能力的人使用的，并不能像各种cms，ecshop，微擎一样部署上就能用。这个项目的价值在于如何让开发更快速，让编码更简单，熟练thinkphp的同学应该从部署到使用都没有任何难度才对。

#### 项目介绍

码如其名：清淡的后台。还望留步给个star，也欢迎加群交流667364680。

发布一个通知，本项目已经进化到了2.0版本了，还会不断的继续演进。请多多支持。[后台演示地址](http://liteadmin.dazhetu.cn/)

后台开发框架的轻量实现，后端基于tp5.1；后台 UI 使用了基于 layui 的 [WeAdmin 2.0](https://gitee.com/lovetime/WeAdmin)。

我尽可能提供一个简洁纯净的后台开发框架，只提供一些基础大众的功能，以及一些示例功能，
并没有内置过多的功能组件，如果项目有需要可以自行composer安装相关组件。

本项目创建之初受到了thinkAdmin的启发，如果需要一个功能强大且完备的后台框架，可以参考使用[thinkAdmin](https://demo.thinkadmin.top/admin.html#/admin/index/main.html?spm=m-7)。

本项目致力于满足于小型项目的后台快速开发。

项目V1.0内置了一个博客，提供了两套模版 在blog\config\template.php 中设置模版，2.0不再集成前台模块，基于yaf实现了一个 [博客程序](https://gitee.com/litephp/yafblog) 可以与本后台无缝对接。[在线演示](http://www.dazhetu.cn)

集成了 xunsearch 迅搜中文搜索，没有安装xunsearch的在使用后台文章发表的时候会报错，只需要把迅搜操作索引相关的代码删掉就可以了

如果在linux环境下运行本程序，请在composer安装完成后自行改变runtime/session目录的用户组为web服务的用户组，以免写权限不足。

#### 项目文档

[项目文档](https://gitee.com/litephp/liteadmin/wikis) 会逐渐的丰满起来。再次重申：本项目不是一个拿来配置下就可以建站的CMS，只是一个能够快速开发后台程序的原型框架。

#### 穷人，要饭！

如果您觉得有用，希望捐赠个烧饼

![乞讨](https://images.gitee.com/uploads/images/2019/1010/110729_e40f1dfc_466192.jpeg)