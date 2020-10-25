# fastwork
此框架借鉴了TP5.1的设计，便于自己工作开发中使用，基于swoole4.x 开发的基于swoole_http_server和协程的
轻量级swoole框架，支持DI容器，Redis连接池、ORM数据库连接池、Facade门面、router路由、Cookie、Session、日志Log、Validate验证组件等的PHP微框架

### 要求环境

php => 7.1.0

swoole => 4.1.12

redis => 4.0.3

无需安装redis的php扩展, swoole自带hiredis支持


# 使用方式

`git clone https://github.com/yumufeng/fastwork.git `

然后进入项目，运行 `php start.php` 即可

# 项目目录

```
+ app
  |+ index 模块
     |+ controller   //控制器
        |- Index.php //默认控制器
  |+ views
     |+ index   //控制器
        |- index.php //默认视图
+ public  静态资源放置的目录
+ config  配置目录
  |- app.php //全局基础配置文件
  |- db.php //数据库文件
  |- cache.php //缓存配置文件
  |- 更多配置可以自定义
+ router // 路由配置
+ runtime // 缓存、日志文件目录
+ fastwork // 框架的目录
  |- libarary //框架核心库
+ vendor //composer 第三方依赖
```
# 容器和依赖注入
默认系统会把组件主动注入到容器中，例如我们可以获取到cache组件。
```
Container::get('cache');
```
响应于获取cache组件了

放入到容器中
```
 Container::set('log',Log::class);
```

#门面层 facade

使用参考TP5.1,设计完全参考：https://www.kancloud.cn/manual/thinkphp5_1/353959

# 数据库CURD

## 查询
### 配置数据库
在/config/db.php 里配置

```php
'mysql' => [
        //服务器地址
        'host' => '127.0.0.1',
        //端口
        'port' => 3306,
        //用户名
        'user' => 'root',
        //密码
        'password' => '123456',
        //数据库编码，默认为utf8
        'charset' => 'utf8',
        //数据库名
        'database' => 'fastwork',
        //表前缀
        'prefix' => 'mz_',
        //空闲时，保存的最大链接，默认为5
        'poolMin' => 1,
        //地址池最大连接数，默认1000
        'poolMax' => 1000,
        //清除空闲链接的定时器，默认60s
        'clearTime' => 30,
        //空闲多久清空所有连接,默认300s
        'clearAll' => 300,
        //设置是否返回结果
        'setDefer' => true,
        'reconnect' => 2  //自动连接尝试次数，默认为1次
    ]

```

### 查询单条
```php
<?php
Db::name('user_info')
    ->field('id,username,info')
    ->where(['username'=>'sethink','password'=>'sethink'])
    ->find();
```

### 查询多条
```php
<?php
Db::name('info')
    ->field('id,username,password,info')
    ->select();
```


## 添加

### 添加单条数据

```php
<?php
$data = [
    'username' => 'sethink2',
    'password' => 'sethink2',
    'info'     => 'ceshi2'
];

Db::name('user_info')
    ->insert($data);
```

### 批量添加

```php
<?php
$data = [
    [
        'username' => 'sethink3',
        'password' => 'sethink3',
        'info'     => 'ceshi3'
    ],
    [
        'username' => 'sethink4',
        'password' => 'password4',
        'info'     => 'ceshi4'
    ]
];

Db::name('user_info')
    ->insertAll($data);
```

## 更新数据

```php
<?php
Db::name('user_info')
    ->where(['username'=>'sethink4'])
    ->update(['password'=>'sethink4-4']);
```

## 删除数据

```php
<?php
Db::name('user_info')
    ->where(['username'=>'sethink4'])
    ->delete();
```

## 执行原生SQL语句
Db::query($sql);

## 执行事务

```php
namespace app\index\controller;

use fastwork\Controller;
use fastwork\Db;
use Swoole\Coroutine\MySQL;

class Index extends Controller
{

    public function index()
    {

        $result = Db::transaction(function (MySQL $mysql, \chan $chan) {
            //事务执行代码
        }, function (MySQL $mysql, \chan $chan) {
            //事务执行失败
        });
        //打出执行结果
        var_dump($result);
    }
}
```



# Redis使用


在config/cache.php 配置文件中配置好
###  直接使用redis
使用redis门面，操作可以参考 phpredis方式一致

```
Redis::set('redis',serialize(123123123));
```

```
$data = Redis::get('redis');
$echo = unserialize($data);
```
###  用cache组件

cache组件实现了自动序列化，所以这里不需要进行手动序列化和反序列化


**设置缓存有效期**

```
Cache::set('name',$value,3600);
```
如果设置成功返回true，否则返回false。

**缓存自增**

针对数值类型的缓存数据，可以使用自增操作，例如：

// name自增（步进值为1）
```
Cache::inc('name');
```
// name自增（步进值为3）
```
Cache::inc('name',3);
```

**缓存自减**

针对数值类型的缓存数据，可以使用自减操作，例如：

// name自减（步进值为1）
```
 Cache::dec('name');
 ```
// name自减（步进值为3）
```
Cache::dec('name',3);
```

删除缓存
```
Cache::rm('name');
```


# 路由注册使用
采用 `fastwork\facades\Route` 进行路由支持
如果你不需要进行seo，可以不使用route定义路由，MVC控制器会自动解析识别路由

例子：

支持传参，将/test/12134 路由映射到 index模块 index控制器 index操作上
传递参数p=12134；

这时/test  ， /test/12134 都可匹配到此路由
```
Route::get('/test/:p?', 'index/Index/index');
```

如果强制要传递参数p，则路由可以写成
```
Route::get('/test/:p', 'index/Index/index');
```
GET或者POST路由

//添加一个接受Get请求的路由
```
$router::get('/test', 'index/Index/index');
```
//添加一个接受Post请求的路由
```
$router::post('/test', 'index/Index/index');
```

# Cookie使用
采用`fastwork\facades\Cookie`类提供Cookie支持。
```
// 设置Cookie 有效期为 3600秒
Cookie::set('name','value',3600);
// 设置cookie 前缀为think_
Cookie::set('name','value',['prefix'=>'tp_','expire'=>3600]);
// 支持数组
Cookie::set('name',[1,2,3]);
```
判断
```
Cookie::has('name');
```
// 判断指定前缀的cookie值是否存在
```
Cookie::has('name','think_');
```
获取
```
Cookie::get('name');
```
// 获取指定前缀的cookie值
```
Cookie::get('name','think_');
```
删除
//删除cookie
```
Cookie::delete('name');
```
// 删除指定前缀的cookie
```
Cookie::delete('name','think_');
```
清空
// 清空指定前缀的cookie
```
Cookie::clear('think_');
```

# Session操作

可以直接使用 `fastwork\facades\Session` 类操作`Session`。
Session使用的redis进行存储
基础用法
赋值

// 赋值（当前作用域）
```
Session::set('name','thinkphp');
```
// 赋值think作用域
```
Session::set('name','thinkphp','think');
```
判断是否存在

// 判断（当前作用域）是否赋值
```
Session::has('name');
```
// 判断think作用域下面是否赋值
```
Session::has('name','think');
```
取值

// 取值（当前作用域）
```
Session::get('name');
```
// 取值think作用域
```
Session::get('name','think');
```
如果name的值不存在，返回null。

删除

// 删除（当前作用域）
```
Session::delete('name');
```
// 删除think作用域下面的值
```
Session::delete('name','think');
```

# 日志操作

采用 fastwork\facades\Log 操作日志

支持 `['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG', 'SQL','SWOOLE']`等几种类型
```
Log::error('错误信息');
Log::info('日志信息');
Log::warning('日志信息');
```