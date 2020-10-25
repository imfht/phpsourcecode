## About this backend framework

laraback framework 是一个基于 Laravel 5.3 开发的后台通用系统，其中前端是采用流行的 AdminLTE 搭建。权限模块是采用的 entrust 完成。

laraback framework 目前经过初步的测试，可以作为学习使用，也可以基于此二次开发。

![preview](https://git.oschina.net/muchuanwazi/laraback/raw/master/preview/20170109213513.jpg)

![preview1](https://git.oschina.net/muchuanwazi/laraback/raw/master/preview/20170109213616.jpg)

## requirement

+ PHP >= 5.6.4
+ OpenSSL PHP Extension
+ PDO PHP Extension
+ Mbstring PHP Extension
+ Tokenizer PHP Extension
+ XML PHP Extension
+ Redis
+ MySQL

## Installation

1. git clone this.
2. 修改根目录 .env 配置文件，主要修改好 MySQL 和 Redis 的连接信息。
3. 执行 php artisan migrate
4. 配置您的虚拟主机的根目录到 public 下。
5. 访问 http://***/login ，使用用户名 admin@example.com ，admin 登录。

## Usage

访问 http://***/login ，使用用户名 admin@example.com ，admin 登录。

1. 新建模块： 首先在模块管理页面新建模块，主要填写模块名称那一栏。然后在 routes/web.php 中定义您的新路由，如果该路由是 resource 模式，建议在对应的 Controller 的 __construct 方法中增加 $this->middleware("permission:模块名称"); 形式增加权限验证。
如果路由是 get 或者 post 模式，直接在路由后应用 ->middleware('permission:模块名称') 即可。可以查看已经存在的用户模块或者角色模块的源码作为例子。

2. 默认在测试模式下(env 文件的 APP_DEBUG=true 时)，ID为1的帐户将不受权限系统限制，登陆后即拥有所有操作权限。设置为 false 后，此账户也会受到权限系统限制。该特性在测试开发阶段，以及上线后不小心设置错误取消掉所有人权限后恢复各个角色权限时比较有用。

3. 可以在 config/system.php 里修改程序名称。

4. 开始编写您自己的程序代码！

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
