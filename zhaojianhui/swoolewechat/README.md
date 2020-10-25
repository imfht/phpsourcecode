# swooleWechat
本项目使用swoole framework官方框架开发，在不改动原框架基础上进行优化，目前的优化主要是控制器模块支持、配置文件按环境不同划分.
本项目主要为了节省微信服务号的开发成本，采用swoole framework提高项目并发处理能力。
项目特色：异步处理、异步MySQL、异步Redis、数据库连接池

框架项目地址：(https://github.com/swoole/framework)

Swoole IDE智能提示
----
```shell
https://github.com/eaglewu/swoole-ide-helper
```

[框架介绍](https://github.com/swoole/framework)

runkit扩展安装(热部署)，目前某些runkit方法报错，无法正常使用
```sh
git clone --depth=1 -v git@github.com:runkit7/runkit7.git /tmp/runkit-ext
cd /tmp/runkit-ext && phpize && ./configure && sudo make && sudo make install
```
为了方便开发，需要在开发环境手动增加一些声明，方便ide自动提示：
如果添加自定义全局对象，也可以自行添加声明。
```sh
#1、vendor/matyhtf/swoole_framework/libs/Swoole/Swoole.php的
 * @property \EasyWeChat\Foundation\Application $easywechat
 * @property \App\Component\Event $myevent
 * @property \App\Component\RabbitMQ $rabbitmq
 * @property \App\Common\Strip $strip
 * @property \App\Component\RBAC $rbac

#2、vendor/matyhtf/swoole_framework/libs/lib_config.php的model方法的注释部分return参数改为如下
@return \App\Component\BaseModel

```

框架目录介绍
----
```sh
├── apps                    项目目录
│   ├── classes             类目录，可以任意添加想要的目录或脚本，swoole会自动加载并注册命名空间
│   │   ├── BaseController  基类控制器目录了
│   │   ├── Component       组件目录
│   │   ├── DAO             数据访问层目录
│   │   ├── Handler         事件执行脚本目录，用来运行定时任务
│   │   ├── Observer        未知
│   │   ├── Queue           自定义队列类目录
│   │   ├── Router          自定义路由控制目录，需要在对应的Server.php中加入Swoole::getInstance()->addRouter(new App\Router\ModuleRouter(), true);
│   │   └── Service         业务逻辑层目录，专门放置业务逻辑代码
│   │   └── WechatHandler   微信事件处理代码，根据不同的消息、事件类别划分
│   ├── configs             配置文件目录，某些配置可以共用，无需在子目录中区分，具体实现自行控制
│   │   ├── devlop          开发环境配置
│   │   ├── product         生产环境
│   │   └── test            测试环境
│   ├── controllers         控制器目录
│   │   ├── Admin           Admin模块控制器目录
│   │   ├── Api             Api模块控制器目录
│   │   └── Index           Index模块控制器目录
│   ├── events              事件定义脚本目录，需要启动事件功能，具体参考server/README.md文件
│   ├── factory             添加与http请求无关全局对象，否则http请求的数据无法销毁，造成代码执行无法正确路由
│   ├── models              数据库访问模型目录
│   └── templates           模板文件目录
│       ├── Admin
│       ├── Index
│       ├── page
│       └── user
├── public                  网站公共文件目录，nginx的root配置指向到这里
├── server                  swoole启动服务脚本目录
│   ├── database            数据库定义目录
│   ├── pid                 swoole启动服务的进程ID保存目录
│   └── ssl                 https证书目录
└── vendor                  composer包目录
```
[服务脚本说明](server/README.md)

NGINX配置
----
{youDomain}、{youPath}改成自己环境的配置
```sh
server {
    listen       80;
    server_name  {youDomain}.cn;
    root   /{youPath}/swooleproject/public/;

    #charset koi8-r;

    access_log  logs/{youDomain}-access.log;
    error_log   logs/{youDomain}-error.log error;

    location / {
        index index.html;
        if (!-e $request_filename){
            proxy_pass http://127.0.0.1:9501;
        }
    }

    location /index.html {
         proxy_pass http://127.0.0.1:9501/;
    }

    #error_page  404              /404.html;

    # redirect server error pages to the static page /50x.html
    #
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   html;
    }
}
```
构造全局对象
----
1、如需配置项则在apps/config/中添加对应的配置文件
2、在apps/factory/目录中添加注册对象，文件名即是全局对象名，如rabbitmq.php
3、使用时则\Swoole::$php->rabbitmq->{method}方式使用

如何自定义异步处理类
----
1、定义配置，则在apps/configs/中定义配置，如rabitmq.php的event键的配置<br>
2、消息队列类，如apps/classes/Queue/RabbitMQ.php,该类需实现\Swoole\IFace\Queue接口<br>
3、调整event配置，首先修改对应的配置目录中event.php中的type消息队列类为App\Queue\RabbitMQ::class<br>
4、定义事件名，在apps/events/中定义事件处理类，一个事件可以同时丢给多个类处理<br>
5、处理事件，在apps/classes/Handler/编写实现Swoole\IFace\EventHandler接口的类
6、触发事件，Swoole::$php->event->trigger("hello2", array('key1'=>'val1','key2'=>'val2'));

启动http服务
----
执行如下脚本会自动显示使用帮助：
```sh
php server/appServer.php --help
```
启动异步处理服务
----
执行如下脚本会自动显示使用帮助：
```sh
php server/eventWorkersServer.php --help
```


