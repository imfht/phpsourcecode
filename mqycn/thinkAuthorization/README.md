#thinkAuthorization

针对 ThinkPHP5 的版本 thinkAuthorization5 已经发布( http://git.oschina.net/mqycn/thinkAuthorization5 )


#1、更新权限表

使用ThinkPHP 开发时，新增加 Controller，或者 修改 Controller 里的 Action。

Authorization -> Update() 可以扫描代码的改动，自动保存到 后台权限表中。

Authorization -> FriendlyName() 可以对权限项目进行自定义改名。

#2、权限拦截的方法

在 进入控制器的 初始化时，通过调用  **权限行为类** <AuthorizationBehavior> 判断当前用户是否在 权限表中有 访问的权限。如果没有权限，则 跳转到 指定的 页面。 



已包含完整的代码演示。



#源代码

**后台权限设置**  /src/Admin/Controller/AuthorizationController.class.php

**权限行为类**    /src/Common/Behavior/AuthorizationBehavior.class.php

**权限表设置**    /src/thinkAuthorization.sql


#使用方式（整理中）：

[ThinkPHP3.2.x](document/3.2.md)

[ThinkPHP3.1.x](document/3.0.md)

[ThinkPHP3.0](document/3.0.md)

#测试(使用ThinkPHP3.2)

请将 当前目录设置为 根目录，访问 http://您的IP/demo 测试

1、参考 [/demo/ThinkPHP/请下载ThinkPHP3.2核心版.md](/demo/ThinkPHP/请下载ThinkPHP3.2核心版.md)下载核心库

2、导入 /src/thinkAuthorization.sql 到数据库中

图文说明请参考：
[thinkAuthorization图文说明](http://www.miaoqiyuan.cn/p/thinkAuthorization)