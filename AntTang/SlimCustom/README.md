# SlimCustom 2.0 Framework

轻量级RESTFul开发框架 SlimCustom 可以帮助你快速编写简单但功能强大的 web 应用和 API。
特色：HTTP 路由，中间件，PSR-7 支持，依赖注入，门面模式，模型自动验证，字段预处理，多进程任务管理...

技术交流群 637251928 ![image description](./qrcode.png)

## 开始使用

```
注意：{$变量}需要替换成实际的值，拒绝无脑复制
```

#### 安装 & 卸载

```
$SlimCustomFrameworkPath/bin/cmd $application app:make    	安装
$SlimCustomFrameworkPath/bin/cmd $application app:remove	卸载
```

#### web服务器

###### Nginx 配置
这是一个例子，在 Nginx 虚拟主机上针对域名 example.com 的配置。它监听80端口上的入境（inbound）HTTP 连接。它假定一个PHP-FPM服务器在端口9000上运行。你需要将 server_name, error_log, access_log, 和 root 这些指令修改成你自己的值。其中 root 指令是你的应用程序公共文件根目录的路径；你的 Slim 应用的 index.php 前端控制器文件应该放在这个目录中。

```
server {
    listen 80;
    server_name example.com;
    index index.php;
    error_log /path/to/example.error.log;
    access_log /path/to/example.access.log;
    root /path/to/public;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        fastcgi_pass 127.0.0.1:9000;
    }
}

```

###### slim文档首页

<http://www.slimphp.net/docs/start/web-servers.html>

## 目录结构

######框架
```
/bin/cmd										命令入口文件（linux）
/bin/cmd.bat									命令入口文件（windows）
/bootstrap/autoload.php							自动载入
/bootstrap/dependencies.php						框架依赖
/config/application.php							暂时不用
/config/configs.php								默认配置文件
/config/errors.php								错误配置
/config/validation.php							自动验证配置
/demo/											应用demo
/docs/Api/										基于phpDoc2生成的php接口文档
/Libs/Cache/Cache.php							缓存
/Libs/Console/Console.php						控制台
/Libs/Console/Daemon.php						任务管理（支持进程管理）
/Libs/Container/Container.php					容器
/Libs/Contracts/								约定
/Libs/Controller/Api.php						接口控制器
/Libs/Controller/Controller.php					控制器抽象类
/Libs/Curl/Curl.php								curl
/Libs/Filesystem/Filesystem.php					文件
/Libs/Exception/SlimCustomException.php			异常
/Libs/Handlers/Error.php						异常处理
/Libs/Handlers/PhpError.php						php错误处理
/Libs/Helpers/Facades.php						助手门面
/Libs/Helpers/Helpers.php						助手函数
/Libs/Http/Response.php							响应
/Libs/Model/Query/PdoQuery.php					PDO查询类
/Libs/Model/Query/MongodbQuery.php				Mongodb查询类
/Libs/Model/Model.php							数据模型
/Libs/Pageinator/Pageinator.php					分页
/Libs/Session/Session.php						session
/Libs/Support/Arr.php							数组
/Libs/Support/Collection.php					集合
/Libs/Support/MessageBag.php					消息包
/Libs/Support/Str.php							字符串
/Libs/Traits/Macroable.php						
/Libs/Traits/Single.php							单例
/Libs/Valication/Validator.php				    验证器
/Libs/App.php									应用核心类
/vendor/										vendor
/index.php										入口文件
/README.MD										文档

```

######Demo应用
```
/bootstrap/routes.php							路由配置
/configs/configs.php							配置文件
/Console/Console.php							应用控制台
/Console/Commands/Hello.php					    命令文件 (hello 命令)
/Controller/									控制器
/Controller/Admin								控制器分组
/Controller/Admin/Index.php						Admin分组下的Index控制器
/data/logs/										日志生成目录
/data/cache/									缓存生成目录
/data/daemon/									任务进程信息生成目录
/docs/											docs
/Middlewares/									中间件
/Middlewares/Admin/								中间件分组
/Middlewares/Admin/Index.php					Admin分组下的Index中间件
/Models/										模型目录
/scripts/										脚本目录
/scripts/Demo.php								Demo任务文件
/tests/											测试
/public/										开放目录
/public/views/									视图目录
/public/index.php								入口文件
/public/.htaccess								.htaccess文件
```

## 配置

######通用

```
// set to false in production
'displayErrorDetails' => true,

// Allow the web server to send the content-length header
'addContentLengthHeader' => false,

// Renderer settings
'renderer' => [
    'template_path' => App::publicPath() . '/views/'
],

// Monolog settings
'logger' => [
    'name' => App::name(),
    'path' => App::dataPath() . '/logs/' . App::name() . '_' . date('Ymd') . '.log',
    'level' => \Monolog\Logger::DEBUG
],

// session
'session' => [
    'driver' => 'cache',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => App::dataPath() . '/sessions/',
    'connection' => null,
    'table' => 'sessions',
    'lottery' => [
        2,
        100
    ],
    'cookie' => App::name() . '_session',
    'path' => '/',
    'domain' => 'hoge.cn',
    'secure' => false
],

// 缓存
'cache' => [
    'default' => 'file',
    'prefix' => App::name(),
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => App::dataPath() . 'cache'
        ],
        'redis' => [
            'driver' => 'redis',
            'cluster' => false,
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    'database' => 0,
                    'password' => ''
                ]
            ]
        ]
    ]
],

// 数据库
'database' => [
    'orm' => 'PDO',
    'default' => 'mysql',
    'prefix' => 'mxu_',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'mxu_message_collect',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict' => false
        ]
    ]
]
```

######路由文件
```
namespace Demo\bootstrap;

use \SlimCustom\Libs\App;

// Routes Example
App::get('/[{name}]', function ($request, $response, $args) {
    //Demo log message
    $this->logger->info("Demo-Skeleton '/' route");
    //Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

App::get('/hello/{name}', function ($request, $response, $args) {
    return 'hello world';
});

App::group('/admin', function () {
    $this->get('/index/{name}', \Demo\controllers\Admin\Index::class . ':index');
    $this->post('/post', \Demo\controllers\Admin\Index::class . ':index');
    //$this->get('/index/{name}', \Demo\controllers\Admin\Index::class . ':index')->add(\Demo\middlewares\Admin\Index::class . ':index');
});
```

######多任务配置
```
$daemon = Daemon();

$daemon->group('Demo', function () {
    // 注册任务‘task1’，每隔10秒运行
    $this->call(10, 'task1', function () {
        while (true) {
            logger()->info(getmypid());
            sleep(1);
        }
        //sleep(10);
        exit;
    });
    // 注册任务‘task2’，每隔5秒运行
    $this->call(5, 'task2', function () {
        while (true) {
            logger()->info(getmypid());
            sleep(1);
        }
        //sleep(5);
        exit;
    });
});

// 启动任务调度守护进程
$daemon->run();
```

## 控制器，模型，缓存等调用

```
<?php
/**
 * @package     index.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月2日
 */

namespace Demo\Controllers\Admin;

use Demo\Models\MessageConfigs;
use SlimCustom\Libs\Paginator\Paginator;
use SlimCustom\Libs\Model\Query\PdoQuery as Query;
use SlimCustom\Libs\Controller\Api;
use Demo\Interfaces\Admin\Index as IndexInterface;
use SlimCustom\Libs\Exception\SlimCustomException;

/**
 * Controller Example
 *
 * @author Jing <tangjing3321@gmail.com>
 */
class Index extends \SlimCustom\Libs\Controller\Api implements IndexInterface
{

    /**
     * Model MessageConfigs
     *
     * @var \Demo\Models\MessageConfigs
     */
    protected $messageConfigs;

    /**
     * construct 依赖注入
     *
     * @param MessageConfigs $messageConfigs            
     */
    public function __construct(MessageConfigs $messageConfigs)
    {
        parent::__construct();
        $this->messageConfigs = $messageConfigs;
    }

    /**
     * Action Example
     *
     * @param Request $request            
     * @param Response $response            
     * @param array $args            
     */
    public function index(\Slim\Http\Request $request, \SlimCustom\Libs\Http\Response $response, $args)
    {
        // Cache
        cache()->put('Tokens.timestamp', time(), 1);
        $timestamp = cache()->get('Tokens.timestamp', null);
        // var_dump($timestamp);die;
        
        // Session
        session()->set('User.user_id', 12345);
        // var_dump(session()->all());die;
        
        // Validator
        $validator = validator(request()->getParams(), [
            'key' => 'required|integer'
        ]);
        // var_dump($validator->messages());die;
        
        // Curl
        // $res = curl()->post('http://mxuapi-team.cloud.hoge.cn/api/tuji/detail/57?access_token=8925a79d6a0377211d0bdbc00a5734e')->response;
        // var_dump($res);die;
        
        // Model
        try {
            // mysql查询
            // 绑定闭包处理rows
            $closure = function (\SlimCustom\Libs\Support\Collection $row) {
                $this->configs = unserialize($this->configs);
                return $this;
            };
            $res = model('configs')->where('id', '<', 12)
                ->bind($closure)
                ->rows();
            // 插入
            $res = model('configs')->rules([
                'name' => 'required|string'
            ])->create($request->getParams());
            // 更新
            $res = model('configs')->rules([
                'id' => 'required|integer'
            ])->renew($request->getParams());
            // 删除
            $res = model('configs')->rules([
                'id' => 'required|integer'
            ], $request->getParams())
                ->where('id', '=', $request->getParam('id'))
                ->remove();
            // 静态方法连贯调用
            $res = MessageConfigs::where('id', '<', 12)->rows();
            // 使用注入对象
            $res = $this->messageConfigs->where('id', '<', 12)
                ->bind($closure)
                ->rows();
            // 使用query对象
            $res = MessageConfigs::query(function (Query $query) {
                // Sql
                $item = $query->select()
                    ->limit(Paginator::COUNT, intval(request()->getParam('page', 1)) * Paginator::COUNT - Paginator::COUNT)
                    ->execute()
                    ->fetchAll();
                // Page
                return new Paginator($item, Paginator::COUNT, request()->getParam('page', 1), [
                    'mode' => 'list',
                    'isAll' => request()->getParam('is_all', false)
                ]);
            });
            
            // Mongodb
            // 查询多个
            $res = model('runoob')->rows([
                'filter' => [
                    'create_user_id' => 1
                ]
            ]);
            // 查询单个
            $res = model('runoob')->row([
                'filter' => [
                    'create_user_id' => 1
                ]
            ]);
            // 创建
            $res = model('runoob')->create($data)->isAcknowledged();
            // 更新
            $res = model('runoob')->renew([
                '$set' => $data
            ], [
                'create_user_id' => 1
            ]);
            // 删除
            $res = model('runoob')->remove([
                'create_user_id' => 1
            ]);
            // 原生方法调用
            $res = model('runoob')->find([
                'site_id' => 1
            ])
                ->toArray()
                ->statementResolve();
            
            // Response
            return response()->success($res->toArray());
        }
        catch (SlimCustomException $e) {
            return response()->error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Renderer Example
     *
     * @param \Slim\Http\Request $request            
     * @param \SlimCustom\Libs\Http\Response $response            
     * @param array $args            
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function renderer(\Slim\Http\Request $request, \SlimCustom\Libs\Http\Response $response, $args)
    {
        return renderer()->render(response(), 'index.phtml', $args);
    }
}

```

##任务管理

######进程结构关系
```
master(任务调度)-> forker (任务1)
               -> forker (任务2)
```

######帮助
```
JingTongdeMac-mini:www jingtong$ php Demo/scripts/Demo.php help
命令       描述             使用
kill      关闭任务          kill  任务名称
killall   关闭所有任务
start     启动任务          start 任务名称
startall  启动所有任务
list      任务列表
help      帮助
JingTongdeMac-mini:www jingtong$ 
```

######列出任务
```
JingTongdeMac-mini:www jingtong$ php Demo/scripts/Demo.php list
task     process         lastRunTime                   stop
task1    36639           2017-07-05 05:59:03           0
task2    36640           2017-07-05 05:59:03           0
```

######启动所有任务
```
JingTongdeMac-mini:www jingtong$ php Demo/scripts/Demo.php startall
task      result
task1     1
task2     1
JingTongdeMac-mini:www jingtong$ ps aux | grep php
jingtong         13878   0.0  0.1  2566948   4644   ??  Ss    3:59下午   0:00.01 php Demo/scripts/Demo.php startall
jingtong         13877   0.0  0.1  2566948   4636   ??  Ss    3:59下午   0:00.01 php Demo/scripts/Demo.php startall
jingtong         13876   0.0  0.0  2566948   2488   ??  Ss    3:59下午   0:00.01 php Demo/scripts/Demo.php startall
```
######关闭所有任务
```
JingTongdeMac-mini:www jingtong$ php Demo/scripts/Demo.php killall
task      result
task1     1
task2     1
JingTongdeMac-mini:www jingtong$ ps aux | grep php
jingtong         13876   0.0  0.0  2566948   2552   ??  Ss    3:59下午   0:00.09 php Demo/scripts/Demo.php startall
```

######关闭单个任务
```
JingTongdeMac-mini:www jingtong$ php Demo/scripts/Demo.php kill task1
JingTongdeMac-mini:www jingtong$ ps aux | grep php
jingtong         13878   0.0  0.1  2566948   4644   ??  Ss    3:59下午   0:00.05 php Demo/scripts/Demo.php startall
jingtong         13876   0.0  0.0  2566948   2500   ??  Ss    3:59下午   0:00.05 php Demo/scripts/Demo.php startall
```
######启动单个任务
```
JingTongdeMac-mini:www jingtong$ php Demo/scripts/Demo.php start task1
JingTongdeMac-mini:www jingtong$ ps aux | grep php
jingtong         13890   0.0  0.1  2566948   4636   ??  Ss    4:02下午   0:00.01 php Demo/scripts/Demo.php startall
jingtong         13878   0.0  0.1  2566948   4656   ??  Ss    3:59下午   0:00.06 php Demo/scripts/Demo.php startall
jingtong         13876   0.0  0.0  2566948   2544   ??  Ss    3:59下午   0:00.07 php Demo/scripts/Demo.php startall
```