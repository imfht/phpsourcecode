# 关于PTWebServer
PTWebServer是一款基于php swoole扩展应用的web http服务器，应用程序可以常驻在内存，可以快速响应客户的请求。
交流QQ群：68832889
完整文档：http://www.haodocs.com/show/ptwebserver
好文档 http://www.haodocs.com 网站全部采用ptwebserver架构

### 全新编程方式

------------
### 主要特性

- 支持虚拟机
- 支持重写（自定义）
- 支持浏览器缓存
- 自动支持文件`Content-Type`类型

# 程序启动
### PHP环境
在linux 环境中，确保shell 环境下可以执行`php`命令，如果不可以执行，那么请在PATH 环境变量上加PHP安装路径。
#### SWOOLE 扩展
PTWebserver http服务器是基于[swoole](http://www.swoole.com "swoole")扩展开发，所以请确保安装好swoole php扩展

>`chmod +x server` 给server程序文件加上可执行权限

> `./server start `启动服务器程序

> `./server stop ` 停止服务器

> `./server restart ` 重启服务器


# 服务配置
PTwebServer 支持网站功能，各个网站的相关配置放在一个配置文件里。
配置文件返回一个配置数组。

#### 默认服务器端口
`'port' => '80' ` 端口
#### 服务器IP
`'listen_ip'=>'0.0.0.0'`  默认监听的IP
#### 工作进程数量
`'task_worker_num' => 5`
#### 最大内存(M)
`'max_memory'=>5`//最大内存（M），当前进程占用内存超过这个值后，进程会退出释放内存。

#### 服务器日志文件
`'log_file'=>'/var/log/swoole.log'` 当服务器处于后台运行时，所有的错误输出日志都保存在此日志文件里,默认：`/tmp/swoole.log`

#### 守护进程
`'daemonize' => true`  ,`true`守护进程,`flase` 非守护进程(默认)
# 虚拟主机配置
`'web' => ['网站1'=>[]] `

  网站配置优先权高过于系统默认配置
#### 301跳转
`'redirect'=>['haodocs.com'=>'http://www.haodocs.com'],`
> 301 跳转,`haodocs.com` 是要跳转的域名，`http://www.haodocs.com` 要跳转的目标域名
> **注意**：`haodocs.com` 也要加到 `server_name` 中

```php
'主机1'=>[
	'port'=>'80',//网站端口，覆盖服务配置的端口
	'document_root'=>'/www/web/',//虚拟主机的根目录
	'server_name'=>'www.abc.com',//域名，多个域名用“,”分开
	'rewrite' => TRUE,//重写功能，false 关闭 ,true 打开
	'index' => 'index.php index.html index.htm',//目录默认访问文件名,多个用空格隔开
	//重写规则
	'rewriteRoute' => [
		//U是控制器参数，/app/control/action 三个层次
		'/^\/(.*?)\?([^\.].*?)$/iU' => '/index.php?U=$1',
		'/^\/(.*?)$/iU' => '/index.php?U=$1',
	],
	'data_timezone' => 'RPC',//php程序的默认时区
	'IsCache' => false,//静态文件是否开启服务器缓存
	/**
	* 浏览器304缓存
	* type 缓存的文件类型
	* time 缓存的时长（s）
	*/
	'Cache' => [
		'type' => ['js', 'css', 'png', 'ico', 'jpg', 'gip', 'wff2'],//缓存的静态文件类型
		'time' => 1000,//缓存时间长秒
	],
	'access_denied' => '',//拒绝访问的文件类型,空格隔开多个
	'exit_process' => 0,//执行处理php后，是否退出进程,默认是退出
	'redirect'=>['haodocs.com'=>'www.haodocs.com'],//301 跳转
]
```


```php
return [

    'port'           => 880,//端口
    'listen_ip'      => '127.0.0.1',//监听ip
    'php_map'        => 'php',//php文件类型映射,如是 html ，则 index.html 映射到index.php文件
    'pt_http_server_key' => 'pt_http_server',//在$_SERVER 下专用这个保存与应用程序的交互
    'max_memory'=>10,//最大内存
    //

    'package_max_length'=>8388608,

       'swoole'=>[
                'worker_process' => 2,//工作进程
                'task_worker_num'=>2,//task 任务进程数量
                'user' => 'nginx',
                'group' => 'nginx',
                'daemonize' => 1,//守护进程
                'log_file'=>'/var/log/swoole.log',//服务运行错误日志
        ],
     'web'=>[
        'movie.com'     => [
            //如果为空，上级http服务有指定 HTTP_DOCUMENT_ROOT,则取之
            'document_root' => '/www/web/xsh_movie/',
            'server_name'   => 'tv.xshapp.com',//访问域名,多个域名用“,”分开

            'rewrite'       => TRUE,//重写功能，false 关闭 ,true 打开
            'index'         => 'index.php index.html index.htm',//目录默认访问文件名,多个用空格隔开
            //重写规则
            'rewrite_route'  => [//U是控制器参数，/app/control/action 三个层次
                                 '/^\/(.*?)\?([^\.].*?)$/iU' => '/index.php?U=$1',
                                 '/^\/(.*?)$/iU'             => '/index.php?U=$1',
            ],
            'data_timezone' => 'RPC',
            'is_cache'=>1,
            /**
             * 浏览器缓存
             * type 缓存的文件类型
             * time 缓存的时长（s）
             */
            'cache'=>[
                'type'=>['js','css','png','ico','jpg','gip','wff2'],'time'=>3
            ],
            'access_denied' => '',//拒绝访问的文件类型,空格隔开多个
            'exit_process'  =>1,//执行处理php后，是否退出进程,默认是退出
            //'redirect'=>['haodocs.com'=>'www.haodocs.com'],//301 跳转
            'session_expire'=>360000,//session 有效期
        ]
        ],
];

```