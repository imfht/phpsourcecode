ThinkPHP 5.1
===============

ThinkPHP5.1对底层架构做了进一步的改进，减少依赖，其主要特性包括：

 + 采用容器统一管理对象
 + 支持Facade
 + 注解路由支持
 + 路由跨域请求支持
 + 配置和路由目录独立
 + 取消系统常量
 + 助手函数增强
 + 类库别名机制
 + 增加条件查询
 + 改进查询机制
 + 配置采用二级
 + 依赖注入完善
 + 中间件支持（V5.1.6+）


> ThinkPHP5的运行环境要求PHP5.6以上。


## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─common             公共模块目录（可以更改）
│  ├─module_name        模块目录
│  │  ├─common.php      模块函数文件
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  │  └─ ...            更多类库目录
│  │
│  ├─command.php        命令行定义文件
│  ├─common.php         公共函数文件
│  └─tags.php           应用行为扩展定义文件
│
├─config                应用配置目录
│  ├─module_name        模块配置目录
│  │  ├─database.php    数据库配置
│  │  ├─cache           缓存配置
│  │  └─ ...            
│  │
│  ├─app.php            应用配置
│  ├─cache.php          缓存配置
│  ├─cookie.php         Cookie配置
│  ├─database.php       数据库配置
│  ├─log.php            日志配置
│  ├─session.php        Session配置
│  ├─template.php       模板引擎配置
│  └─trace.php          Trace配置
│
├─route                 路由定义目录
│  ├─route.php          路由定义
│  └─...                更多
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~

> router.php用于php自带webserver支持，可用于快速测试
> 切换到public目录后，启动命令：php -S localhost:8888  router.php
> 上面的目录结构和名称是可以改变的，这取决于你的入口文件和配置参数。

## 升级指导

原有下面系统类库的命名空间需要调整：

* think\App      => think\facade\App （或者 App ）
* think\Cache    => think\facade\Cache （或者 Cache ）
* think\Config   => think\facade\Config （或者 Config ）
* think\Cookie   => think\facade\Cookie （或者 Cookie ）
* think\Debug    => think\facade\Debug （或者 Debug ）
* think\Hook     => think\facade\Hook （或者 Hook ）
* think\Lang     => think\facade\Lang （或者 Lang ）
* think\Log      => think\facade\Log （或者 Log ）
* think\Request  => think\facade\Request （或者 Request ）
* think\Response => think\facade\Reponse （或者 Reponse ）
* think\Route    => think\facade\Route （或者 Route ）
* think\Session  => think\facade\Session （或者 Session ）
* think\Url      => think\facade\Url （或者 Url ）

原有的配置文件config.php 拆分为app.php cache.php 等独立配置文件 放入config目录。
原有的路由定义文件route.php 移动到route目录

## 命名规范

`ThinkPHP5`遵循PSR-2命名规范和PSR-4自动加载规范，并且注意如下规范：

### 目录和文件

*   目录不强制规范，驼峰和小写+下划线模式均支持；
*   类库、函数文件统一以`.php`为后缀；
*   类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
*   类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名
*   类的命名采用驼峰法，并且首字母大写，例如 `User`、`UserType`，默认不需要添加后缀，例如`UserController`应该直接命名为`User`；
*   函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 `get_client_ip`；
*   方法的命名使用驼峰法，并且首字母小写，例如 `getUserName`；
*   属性的命名使用驼峰法，并且首字母小写，例如 `tableName`、`instance`；
*   以双下划线“__”打头的函数或方法作为魔法方法，例如 `__call` 和 `__autoload`；

### 常量和配置
*   常量以大写字母和下划线命名，例如 `APP_PATH`和 `THINK_PATH`；
*   配置参数以小写字母和下划线命名，例如 `url_route_on` 和`url_convert`；

### 数据表和字段
*   数据表和字段采用小写加下划线方式命名，并注意字段名不要以下划线开头，例如 `think_user` 表和 `user_name`字段，不建议使用驼峰和中文作为数据表字段命名。

## 参与开发
请参阅 [ThinkPHP5 核心框架包](https://github.com/top-think/framework)。

## 版权信息

ThinkPHP遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2006-2018 by ThinkPHP (http://thinkphp.cn)

All rights reserved。

ThinkPHP® 商标和著作权所有者为上海顶想信息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)

### 关于dawn-api更改的部分

> 这里再次对dawn-api进行了改动,下面就更改的一些地方进行说明

1. 优化并使用了自动加载扩展函数,解放了每次都要手动在`extraActionList`书写的问题
2. Wiki文档可以使用注解进行标记了,解放了需要在`getRules()`中写参数才能显示的问题,当然,只是做优化,您还可以写在里面,在这里我主要使用注解方式.下面是注解的用法,以login为例:
   
   ~~~
   /**
         * @title 用户登录
         * @method login 
         * @param string $phone 账号 true
         * @param string $password 密码 true md5
         * @route('v1/user/login')
         * @return Object User 用户信息
       */
    ~~~
    
   * @title 接口名称
   * @method 注解的方法,表现为Wiki方法`login`的接口参数说明
   * @param 参数列表,以一个空格隔开.形如:[数据类型 形参 参数名称 是否必须 备注说明 取值范围]
   * @route 注解路由,在wiki的表现形式是将路由显示在方法后面
   * @return 返回的数据类型,以一个空格隔开.形如:[数据类型 参数名称 备注说明]
   * 完成后的效果:
  
   ![注解显示效果](http://api.jswei.cn/images/login.png)

3. 配合使用ThinkPHP5.1.X的注解路由更家灵活多变,上面的例子就用到了注解路由,更多有关于注解路由[请参考](https://www.kancloud.cn/manual/thinkphp5_1/469333)

 ### 关于dawn-api说明
 
 1. 为了方便使用这里讲修改过的dawn-api也提交到项目中了
 2. 原先下载是的是没有适配后的dawn-api所以不支持ThinkPHP5.1.X
 3. 关于其他的配置以及wiki的配置,请参见dawn-api的说明
 4. 修改的不太成熟,如有问题请提出
 
 ### 新增了命令行工具
 
 >为了方便使用在这里新增了命令行工具,使用命令行工具可以快速的创建API控制器,下面做简单的介绍
 
 1. 进入项目目录后,输入`php ./think`可以查看可以使用的命令行,看到了`api`,就表示可以使用提供的命令行工具了,显示如图:
 
 ![命令行](http://api.jswei.cn/images/line01.png)
 
 2. 使用命令`php ./think api -s first -c news`创建一个命名空间为`first`
 名称为`news`的控制器,运行命令后出现了`Success`就创建成功了.如图:
 
 ![wiki文档](http://api.jswei.cn/images/line02.png)
 
 3. 命令执行成功后你会看到在项目中`application\first\controller`中多出了一个名为`News.php`的文件,这个就是为你生成的一些方法.
 
 4. 在生成控制器的同时还在配置文件`api_doc.php`生成了相应的文档的相关配置,如图:
 
 ![wiki文档](http://api.jswei.cn/images/line03.png)
 
 5. 打开wiki这时就可以看见基本的相关api的文档说明,如图:
 
 ![wiki文档](http://api.jswei.cn/images/line04.png)
 
 6. 这时候点击api地址可以测试是否成功(这里使用的是BaseAuth,浏览器提示登录窗口).如图:
 
 ![wiki文档](http://api.jswei.cn/images/line05.png)
 
 > 需要注意的是因为开启了注解路由,创建成功之后最好执行路由生成工具,重新生成路由.
 
 ### api命令号的说明
 
 > 下面主要介绍一下提供的参数,方便您使用
 
 1. 首先,你可以使用`php ./think api -h`查看帮助,您将得到的结果如下:
 
 ![wiki文档](http://api.jswei.cn/images/line06.png)
 
 * -s or --namespace 生成的控制器的命名空间
 * -c or --controller 生成的控制器的名称
 * -i or --id 生成的文档配置的id,默认是根据原配置一次增加
 * -p or --parent 作为一个文档的子节点的父亲id,默认是0顶级节点
 * 看一些列子
 
 1. ` php ./think api -s first -c sub -p 5 #为id为5的创建一个字文档,id自增`  
 2. `php ./think api -s first -c sub1 -i 8 #创建一个id为8的文档节点,父级为顶级`
 3. `php ./think api -s first -c sub2 -i 9 -p 5 #创建一个id为5的创建一个id为9的子级文档节点`
 
 运行成功后:
 
 ![wiki文档02](http://api.jswei.cn/images/line07.png)
 
 ![wiki文档03](http://api.jswei.cn/images/line08.png)
 
 ![wiki文档04](http://api.jswei.cn/images/line09.png)
 
 ![wiki文档04](http://api.jswei.cn/images/line10.png)
 
### 在线的例子

[在线示例WIKI](http://api.jswei.cn/wiki)

[Postman接口文档](https://documenter.getpostman.com/view/4206182/RW1dHKCt)