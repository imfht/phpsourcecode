# Alpaca-PHP-2.0

## 简介

### Alpaca-PHP-2.0 简介

&emsp;&emsp;Alpaca-PHP 是一款轻量的PHP-MVC框架，确切的说应该是一款MC框架，因为在2.0版本中，去掉了view层，只提供控制器，模型业务逻辑层。
默认情况下，每一个请求返回一个json数据。Alpaca-PHP框架支持composer，使用Laravel-DB（illuminate/database）作为数据库访问层。同时支持有命名
空间，无命名空间两种格式。方便集成各种类库、第三方资源。


## 目录结构

### 1. Alpaca-PHP默认的目录结构


```
-application
　 -modules
　 -resource
　 -service
　  Bootstrap.php
-config
　  main.php
-library
　 -Alpaca
　 -vendor
　  composer.json
　  composer.lock
-public
　  index.php
-runtime
　 -log
　 -cache
```
```
1. 示例中的application一个具体项目应用的目录。

2. application目录下面有三个子目录，1个php文件。
   modules         存放模块相关信息，里面包含控制器，业务逻辑等
   resource        存放资源信息，例如数据库实体类等
   service         存放底层或者公用的业务逻辑、方法，类等
   Bootstrap.php   每一个请求开始执行时候，Bootstrap中每一个以_init开头的方法，会依次调用

3.config存放配置文件
   main.php         存放主要配置信息，任何环境都会使用这个配置
   development.php  存放开发环境配置信息，开发环境会使用这个配置，并且与main.php合并（环境变量MOD_ENV = DEVELOPMENT）
   production.php   存放开生产境配置信息，生产环境会使用这个配置，并且与main.php合并（环境变量MOD_ENV = PRODUCTION时）
   test.php         存放测试环境配置信息，测试环境会使用这个配置，并且与main.php合并（环境变量MOD_ENV = TEST）

4.library中 存放类库，第三方资源等

5.public中 存放应用的入口文件

6.runtime中 存放应用运行时候的文件，例如log，cache等
```

##  交流方式

### 联系我们

详细的文档正在完善中...

作者： Sponge
邮箱： 1796512918@qq.com

