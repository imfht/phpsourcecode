#### 注意，这个项目只是框架内核代码，只能作为依赖，不能直接运行，使用 DEMO 请移步 [http://git.oschina.net/blackfox/herosphp-app](http://git.oschina.net/blackfox/herosphp-app)

为什么要造这个轮子？
====
"PHP是世界上最好的变成语言，没有之一。"这句话虽然是一个梗但是他却代表了php这门语言在编程语言中的江湖地位。那既然是最好的语言，那么免不了就会出现各种各样的框架啦，包括php官方的zendframe, Laravel,Yii,包括国产的thinkPHP,ci等框架。那既然有这么多框架，为什么我们还要造这个轮子呢。原因有以下几点：

1. 最初是基于教学的目的，想作为一个学习型的框架给那些想开发自己的php框架的码农借鉴使用，抛砖引玉。
2. 目前开源的php框架总有这样或者那样的不符合公司项目要求的，使着不顺手，要么就过于重量级，像zendframe,thinkPHP那样太过于臃肿，学习成本太高，要么就是扩展性和或性能达不到要求，使用起来也很不方便。
3. 想把自己学习到的php编程知识做下沉淀，那最好的方法莫过于写php开发框架了。
4. 目前的开源框架都是为了兼容高中低端各种用户而牺牲了框架本身的性能和特性，这显然没有办法完全满足公司的全部需求，而修改像thinkPHP这种大型框架是一件很痛苦的事情，跟自己开发的成本差不多。这估计也是很多大点的公司都有自己的框架的原因了。

------------------

HerosPHP的设计思想
====
HerosPHP是一套web应用开发框架，我们觉得好开发框架应该是<strong>方便，快捷，优雅，干净</strong>的，这也是我们一直在用心做的事情。我们希望框架既能封装良好，是开发者使用方便，但又不损失框架性能，既能快速搭建系统又能保持良好的扩展性。

> 作为一群有逼格的码农，我们目标是开发能够应对<code class="scode">百万级pv</code>的系统框架，并且能够支持多应用。

我们严格遵循约定优于配置的设计原则，能有约定就不提供配置，比如对一些你几乎不会配置的可配置选项，我们都会屏蔽配置接口，按照约定的方式执行，尤其是是3.0.0之后，这个原则会更加明显。配置太多只会是系统越来越臃肿，执行效率越来越低。

我们始终坚持<strong>一个问题只保留一种你能够实现的最优解决方案即可</strong>，所以我们屏蔽了一些不规范的编码习惯，比如说mysql的查询条件几乎所有的框架都兼容了直接写条件的sql语句去查询，但是在herosphp中，对不起，你只能使用我们提供的数组查询语法，这样的设计是第一是为了使代码可读性更高，调理清晰。二是为了兼容mongodb和elasticsearch的查询语法，使得代码更健壮。当然这个见仁见智，认同点赞，不喜勿喷。这里只是举个栗子而已，具体设计请阅读<code class="scode">查询语法</code>。当然，框架里面短期内还是会兼容部分的老的API，但是我们强烈推荐使用新的API。

像其他框架一样，我们也会提供cache（缓存），filter（数据过滤），files（文件处理），session（会话）等开发过程中常用的工具的功能，跟其他框架不一样的是，我们还提供了类似beans的模块，借鉴java中spring框架的模式实现服务资源的托管...

herosphp的特性
=======
1. 部署简单，高开发效率并且高性能
2. 框架的原理简单易懂，容易学习
3. 在保持约定大于配置的原则下又保持着很好的扩展性
4. 代码风格简洁漂亮，程序精简高效，但是注释详细，适合新手学习.


GitHub 源码地址
====
码云: [http://git.oschina.net/blackfox/herosphp](http://git.oschina.net/blackfox/herosphp)

GitHub: [https://github.com/yangjian102621/herosphp](https://github.com/yangjian102621/herosphp)

开发手册
========
http://docs.r9it.com/herosphp/v3.0/


demo 演示地址
=======
### [http://herosphp.r9it.com](http://herosphp.r9it.com)

联系作者
=====
邮箱：<a href="mailto:yangjian102621@gmail.com">yangjian102621@gmail.com</a>

QQ：906388445

技术交流QQ群：137352247

博客 : <a href="http://r9it.com/">小一辈无产阶级码农</a>

#### 本地调试
如果你想在本地直接调试herosphp框架，不想每次都要更新到 composer 仓库，然后再用 composer update 去composer仓库去更新。
（这个过程慢的要死，就算你使用了中国镜像也慢）
你可以通过任何方式将 herosphp 放入你的本地目录，如/code/php/herosphp
然后在你的 app 项目的 composer.json 中加入如下配置

```bash
"repositories": [
        {
            "type": "path",
            "url": "/code/php/herosphp"
        }
    ],
```
然后在 app 根目录下运行

```bash
composer require 'herosphp/framework:*@dev'
```

版本更新记录
======

## version 3.0.5
1. 完成 API 通用网关功能, 可以轻松创建 Http API 服务。不用创建控制器，直接把 service 层变成 API 服务接口。
2. 移除 `src/bean` 组件（其功能已经被 Loader 更好的替代了）
3. 优化异常处理, 修复一些已知的 Bug
4. 更新开发文档

## version 3.0.3
1. 修复 MysqlModel::where 方法中闭包判断的bug， 之前使用的是 is_callable($field), 导致在 $field 是 url 这种函数名的时候会被当做闭包判断。
2. 给 RedisSession 的缓存加上前缀，需要在 app/configs/session.config.php 的 redis session configure 中加上 prefix key，
指定redis session 存储前缀
3. 修改 Filter 类，将一些常用的过滤 API 暴露出去可以作为工具单独使用。
4. 修复 MysqlModel::whereOr 闭包查询bug
5. 更改缓存工厂(CacheFactory)的实现，采用类似动态工厂的方式，如果再新增缓存实现的时候，不需要再更改工厂类的代码。
6. 修复创建多个项目时造成的监听器加载异常的bug

## version 3.0.2
1. 重构了 MysqlModel， 新增了 getSqlBuilder() 和 setSqlBuilder() 方法
2. 修改 JsonResult 的数据结构
3. 增加Session存Redis带前缀prefix功能

## version 3.0.1
1. 修复了 MysqlQueryBuilder::addWhere 方法的bug, 当第三个参数不传入时查询报错。
2. 修复 JsonResult::output 输出日志乱码的bug。
3. 更新了 StringUtils类的生成分布式唯一ID的算法，把32位改成生成18位的16进制数
4. 重要： 给监听器（Listener）新增了skinUrl()接口，用来过滤不需要监听的请求 URI


## version 3.0.0
1. 优化组织结构目录，将框架代码和应用代码完全隔离，更好的支持多应用开发，更好的保护框架的安全性
2. 支持配置多份配置文档，可以同时配置开发环境(dev), 测试环境(test), 生产环境(prod), 大大减少项目上线工作量，可以很方便的使用git的hooks实现自动部署。
3. 优化数据模型接口，统一使用数组作为查询条件，兼容mongoDB和elasticSearch查询语法；新增了MongoModel(mongoDB数据模型)
4. 新增日志类（Log），捕获异常的时候如果是非调试模式会自动记录日志
5. 集成RSA加密工具类实现，新增了签名类，方便调用远程API
6. 实现了同步锁功能，提供 FileSynLock(文件锁) 和 SemSynLock(信号量锁) 2种实现
7. 新增了一些工具类 （ModelTransformUtils等）
8. 修复了一些已知的bug


## version 2.0.0 (重构)
1. 新增了WebApplication 层来控制整个web请求的生命周期，控制器中的每个功能方法都需要传入HttpRequest对象
2. 更新了模板引擎，新增了局部和全局css引入的标签
3. 更改了php文件的加载，所有的php文件都使用Loader加载器来加载，除了核心框架类使用自动加载，其他的类全部是Loader来按需加载，以减少全部类的自动加载的开销。
4. 在根目录下新增了client.php 和 client 目录， 方便执行php的客户端程序。 使用方法详情见操作手册
5. 新增Beans对象管理工具，可以很方便的配置和管理服务。
6. 修改了ImageThumb 类，新增了图片裁剪方法。
7. 重构了缓存模块，新增了缓存的分类，避免了当缓存文件太多的对文件系统inode节点限制，也可以大大提高文件缓存的读写效率。
8. 修改了数据操作模块，新增了对数据库集群的支持，只需要在herosp.const.php中配置将 DB_ACCESS的值改成B_ACCESS_CLUSTERS
9. 在utils中新增了邮件发送服务类 Smtp.class.php
10. 重写了session， 新增了memcache介质存储

## version 1.0.0
实现了框架的基本功能
1. 项目组织结构
2. URL解析
3. 数据DB层的操作
4. MVC设计模式, 自己实现的模板引擎
5. 基本工具类，如果文件上传，图片裁剪，生成缩略图，文件处理等
