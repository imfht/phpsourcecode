# YanPHP V0.2
[Release Note](https://github.com/kong36088/YanPHP/tree/master/doc/Release.md)

# Guide

- [Documentation](#documentation)
    - [安装](#安装)
        - [环境要求](#环境要求)
        - [安装步骤](#安装步骤)
    - [hello world](#hello-world)
    - [路由](#路由)
        - [默认路由规则](#默认路由规则)
        - [自定义路由规则](#自定义路由规则)
    - [配置](#配置)
        - [系统配置相关](#系统配置相关)
        - [日志配置相关](#日志配置相关)
        - [database](#database)
    - [YAssert](#yassert)
    - [入参和Input](#入参和input)
        - [用法介绍](#用法介绍)
        - [相关入参规则](#相关入参规则)
        - [获取输入参数](#获取输入参数)
    - [Database](#database)
        - [使用介绍](#使用介绍)
        - [DB多连接管理](#db多连接管理)
    - [Session](#session)
    - [定制化](#定制化)
        - [定制Result格式](#定制result格式)
        - [定制ReturnCode](#定制returncode)
    - [YanPHP命名规范准则](#yanphp命名规范准则)
        - [控制器](#控制器)
        - [Model](#model)
        - [入参配置文件（`Param/*.ini`）](#入参配置文件（`param/*.ini`）)
    - [安全](#安全)
        - [重新生成SessionID](#重新生成sessionid)
        - [CSRF防御措施](#csrf防御措施)
    - [Nginx](#nginx) 
    - [Apache](#apache)
    - [Tests](#tests)
    - [License](#license)
    

# Documentation

这是一个为API开发而设计的高性能轻量级框架。
框架为你集成了一些常用的类库，让你开发更加便捷。
另外引入了Composer，可以让你更加轻松地管理依赖以及处理命名空间问题。
让语法更加贴近原生PHP，并且在此基础上让你实现自己的定制化功能。
因为封装程度不高，所以框架的性能十分之优秀。


## 目录结构
```
├── Application                  --你的代码目录
│   ├── Cgi                        --分层应用根目录（这里是Cgi代码）
│   │   ├── Cache                    --缓存
│   │   ├── Compo                    --自定义组件
│   │   ├── Config                   --配置
│   │   ├── Controller               --控制器，用于编写业务逻辑
│   │   ├── Logs                     --日志存放
│   │   ├── Model                    --模型层
│   │   ├── Param                    --入参定义，以及参数校验
│   │   └── Util                     --工具类库
│   └── Server                     --分层应用根目录（这里是Server代码）
│   │   ├── Cache                    --缓存
│   │   ├── Compo                    --自定义组件
│   │   ├── Config                   --配置
│   │   ├── Controller               --控制器，用于编写业务逻辑
│   │   ├── Logs                     --日志存放
│   │   ├── Model                    --模型层
│   │   ├── Param                    --入参定义，以及参数校验
│   │   └── Util                     --工具类库
├── System                       --框架目录
│   └── Yan
│       ├── Common
│       └── Core
│           ├── Compo
│           └── Exception

```

## 安装

### 环境要求

Require:

`PHP >= 7.0`

`Composer`

Composer安装：https://getcomposer.org/download/

### 安装步骤

先把`YanPHP`从github拷贝到本地

```bash
git clone https://github.com/kong36088/YanPHP.git
```

然后进行框架依赖组件的安装，这里需要用到`composer`。请确保您的机器中已经安装了`composer`
```bash
composer install
```

完成！

## hello world

首先安装YanPHP的依赖项：
```php
composer install
```

来编写我们的第一个hello world

首先我们需要先新增一个控制器
新建一个控制器文件 `Application/Cgi/Controller/HelloController.php`
``` php
<?php
namespace App\Cgi\Controller;

use Yan\Core\Compo\ResultInterface;
use Yan\Core\Controller;

class HelloController extends Controller
{

    public function index(): ResultInterface
    {
        return $this->succ('hello world');
    }
}
```
紧接着到`Param`目录下创建我们的控制器入参规则，
`Application/Cgi/Param/HelloController.ini`内容如下：
``` ini
[index]
```
用命令行重新加载composer中注册的命名空间：
``` bash
composer dump-autoload
```

最后，用浏览器访问我们刚才编写的hello world：`http://localhost/interface.php/hello/index`

返回结果为以下内容
``` json
{
    "code": 0,
    "message": "hello world",
    "data": []
}
```

若有新增类库文件，**记得一定要运行一次 `composer dump-autoload` 以刷新我们的autoloader**

## 路由

### 默认路由规则
默认路由寻路路径是：/interface.php/controller/method
controller代表您的控制器，method是指向的方法

举例：http://localhost/interface.php/user/getUser
这个路径映射到`UserController`的`getUser`方法

### 自定义路由规则
当然，您也可以自定义自己的路由规则。
路由规则存放在 `Application/YourLevel/Config/router.php` 
``` php
$config['default_method'] = 'index';   //默认方法

$config['route'] = [
    '/' => [    //被映射的路径
        'request_method' => ['GET','POST'],   //支持的http动作，支持GET和POST
        'controller' => 'App\\Cgi\\Controller\\UserController',  //所映射到的控制器，需要包含命名空间，映射到Application/Cgi/Controller/UserController
        'method' => 'index'       //所映射到的方法，映射到UserController的index方法
    ],
    '/user' => [
        'request_method' => ['GET'],    //支持的http动作，支持GET
        'controller' => 'App\\Cgi\\Controller\\UserController',    //所映射到的控制器，需要包含命名空间，映射到Application/Cgi/Controller/UserController
        'method' => 'getUser'     //映射到UserController的index方法getUser方法
    ],
];

```

## 配置

配置文件统一存放在 `Application/YourLevel/Config` 目录下
`Application`下的各个文件夹对应着您应用的各个分层，每一层都采用自己独立的Config配置

#### 系统配置相关
``` php
$config['namespace'] = 'App\\Cgi';
```
这里用于配置你的应用层采用的命名空间，在新添加应用层后请勿忘记修改这里的配置哦。

``` php
$config['session_path'] = BASE_PATH.'/Cache/session';
$config['session_name'] = 'YAN_SESSION';
```
`session_path` 用于配置session存放的僦
`session_name` 用于配置session名称，即用于标识用户session id的key值

#### 日志配置相关

几种日志等级。比如日志等级配置为`INFO`，则INFO及INFO以上的（NOTICE、WARING、ERROR）等等级的日志将会被记录。
```
/**
'DEBUG'
'INFO'
'NOTICE'
'WARNING'
'ERROR'
'CRITICAL'
'ALERT'
'EMERGENCY'
*/

$config['log_level'] = 'DEBUG';
```

日志存放路径
```
/**
 * The log path
 */
$config['log_path'] = BASE_PATH . '/logs/cgi.log';
```

```
/**
 *  最大存放的日志文件数量
 */
$config['log_max_file'] = 0;
/**
 * 配置日志记录的格式
 * "[%datetime%] %channel%.%level_name%: %message% %context%\n";
 */
$config['log_format'] = "[%datetime%]-%extra.process_id% %channel%.%level_name%: %message% %context%\n";
```

#### database

`database.php`

|config|options|description|
|:-----------:|:-----------:|:-----------:|
|`db_host`||DB host|
|`db_user`||用户名|
|`db_password`||密码|
|`db_port`| 3306/(others)|端口号|
|`db_database`||库|
|`db_charset`|utf8/(others)||
|`db_driver`| mysql/postgres/sqlite/sqlsrv |目前支持四种数据库类型|

可以配置多个数据库连接，默认使用`default`进行连接

例如下面的例子：
```php
$config['db'] = [
    'default' => [
        /** host */
        'db_host' => 'mysql',
        /** 数据库用户名 */
        'db_user' => 'root',
        /** 数据库密码 */
        'db_password' => 'root',
        /** 端口 */
        'db_port' => 3306,
        /** 数据库 */
        'db_database' => 'yan',
        /** 表名前缀 */
        'db_prefix' => '',
        /**
         * mysql/postgres/sqlite/sqlsrv
         */
        'db_driver' => 'mysql',
        'db_charset' => 'utf8',
        'db_collation' => 'utf8_unicode_ci'
    ],
    'mysql1'=>[
        'db_host' => '',
        'db_user' => '',
        'db_password' => '',
        'db_port' => 3306,
        'db_database' => '',
        'db_prefix' => '',
        'db_driver' => 'mysql',
        'db_charset' => '',
        'db_collation' => ''
    ]
];
```
这里我们可以对连接进行管理，其中上面的`default`以及`mysql1`是我们的连接名称，我们可以根据名称进行数据库连接的切换。
具体可以看[Database/DB多连接管理](#db多连接管理)


## YAssert

YanPHP内嵌的断言支持。感谢[beberlei/assert](https://github.com/beberlei/assert)提供类库支持

详细的使用方法在这里：[YassertDocument](https://github.com/kong36088/YanPHP/tree/master/doc/YAssert.md)

## 入参和Input

### 用法介绍
所有入参都需要定义在应用目录路径下的Param目录，并且可以对其进行相关的参数校验操作。

下面我们会举例对该功能进行讲解。

例如我们需要请求`UserController`的`index`方法，那么我们需要创建一个`入参配置文件` `Param/UserController.ini`

文件内容如下：
```ini
[index]
user_id="starts_with[1]|required|numeric|between[1,123]"
page="numeric"
domain="string|numeric"
arr="array"

[getUser]

```
“=”号左边的是需要的入参，右边的是需要验证的规则。规则都是`Validator`内置好的，基于[Respect/Validation](https://github.com/Respect/Validation)开发
并且只有被定义在`入参配置文件`中的参数才会被Input类所识别，其余参数一律丢弃。

若参数不符合规则要求，则会直接返回错误信息。

如若你需要为参数配置多个验证规则，可以用 `|` 进行规则分割，例子：`domain="string|length[1,20]"`。
在这个例子中，我们要求domain必须是字符串类型，并且长度在1-20个字符之间。


### 相关入参规则

|规则|参数|使用说明|例子|
|:---:|:---:|:---:|:---:|
|required|否|参数必填||
|optional|否|参数可空||
|integer|否|整型||
|numeric|否|所有字符都是数字（不区分变量类型）||
|float|否|浮点型||
|string|否|字符型||
|array|否|数组型||
|ip|否|验证是否为一个有效的ip||
|json|否|验证是否为合法json格式||
|email|否|验证是否为合法邮箱||
|domain|否|验证是否为合法域名||
|regex|是|正则匹配|`regex(/[0-9]+/)`|
|starts_with|是|是否以规定的字符开头|`starts_with(ab)`|
|ends_with|是|是否以规定的字符结束|`ends_with(ab)`|
|between|是|数值在定义的范围之间|`between(1,100)`|
|min|是|定义最小不小于|`min(1)`|
|max|是|定义最大不大于|`max(100)`|
|length|是|定义字符串长度在定义范围内|`length(1,100)`|
|equal|是|入参的值必须等于定义的值|`equal(123)`|
|contain|是|入参是否包含给出的值|`contain([ab])`|

### 获取输入参数

```php 
Input::get('user_id'); //获取参数user_id

Input::set('user_id',1); //设置参数
```

## Database

DB方面YanPHP采用了[illuminate/Database](https://github.com/illuminate/database)。
编码设计风格与其保持总体一致。


### 使用介绍
> `composer require "illuminate/events"` required when you need to use observers with Eloquent.

Once the Capsule instance has been registered. You may use it like so:

**Using The Query Builder**

```PHP
$users = Capsule::table('users')->where('votes', '>', 100)->get();
```
Other core methods may be accessed directly from the Capsule in the same manner as from the DB facade:
```PHP
$results = Capsule::select('select * from users where id = ?', array(1));
```

**Using The Schema Builder**

```PHP
Capsule::schema()->create('users', function ($table) {
    $table->increments('id');
    $table->string('email')->unique();
    $table->timestamps();
});
```

**Using The Eloquent ORM**

```PHP
class User extends Illuminate\Database\Eloquent\Model {}

$users = User::where('votes', '>', 1)->get();
```

或者使用YanPHP提供的风格
```PHP
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class User extends Model
{
    protected $table = 'user';

    protected $primaryKey = 'uid';

    protected $keyType = 'int';

    public function getById($id): Collection
    {
        return $this->where([$this->primaryKey => $id])->get();
    }

    public function getByCond($cond): Collection
    {
        return $this->where($cond)->get();
    }

    public function updateByCond($cond, $update): bool
    {
        return $this->where($cond)->update($update);
    }

    public function deleteById($id)
    {
        return $this->where($id)->delete();
    }
}


$UserModel = new User();
$UserModel->getById(1); // 获取user表中uid为1的用户数据信息

```

For further documentation on using the various database facilities this library provides, consult the [Laravel database documentation](https://docs.golaravel.com/docs/5.4/database/).

### DB多连接管理

在配置文件`database.php`配置我们的连接后，可以实现多个db连接实例。

下面我们将介绍如何进行连接的切换。

`Model/User.php`
```php
<?php
namespace App\Cgi\Model;

use Illuminate\Support\Collection;
use Yan\Core\Model;

class User extends Model
{
    protected $table = 'user';
    protected $connection = 'mysql1';  //这里可以配置User Model默认使用"mysql1"连接

    public function getById($id): Collection
    {
        //这里可以使当前实例的连接切换为"default"
        $this->setConnection('default');
        
        return $this->where([$this->primaryKey => $id])->get();
    }
}
```

我们可以使用Model当中的`$connection`配置默认的连接。

另外一种方法是使用自带的`$this->setConnection($name)`方法进行连接的设置

## Session

用法示例
``` php
use Yan\Core\Session;

Session::set('a','b');  //设置session值

$sessionVaue = Session::get('a');  //获取session中的值

Session::destroy(); //销毁所有session
```

Session类中有以下方法
``` php
/**
 * @method static mixed get($key, $alt = null)
 * @method static mixed set($key, $val)
 * @method static null clear()
 * @method static mixed getFlash($key, $alt = null)
 * @method static null setFlash($key, $val)
 * @method static null clearFlash()
 * @method static mixed getFlashNext($key, $alt = null)
 * @method static null setFlashNow($key, $val)
 * @method static null clearFlashNow()
 * @method static null keepFlash()
 * @method boo null destroy()
 */
```

## 定制化
### 定制Result格式

可以到你的应用目录下的`Compo/Result.php`定制化你的Result格式

下面是Result类的示例：
``` php
namespace App\Cgi\Compo;

use Yan\Core\Compo\ResultInterface;

class Result implements ResultInterface
{
    protected $code;
    protected $message;
    protected $data;

    public function __construct(int $code, string $message, array $data = [])
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    function getCode(): int
    {
        return $this->code;
    }

    function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return ['code' => $this->code, 'message' => $this->message, 'data' => $this->data];
    }
}
```
`jsonSerialize()`返回一个数组，作为结果输出

### 定制ReturnCode

YanPHP为你定义了一个全局的返回码，返回码的修改可以到`System/Yan/Core/ReturnCode.php`修改

## YanPHP命名规范准则

YanPHP的所有的类文件文件名都 **必须** 与类名保持一致

框架的命名方式应该遵循[驼峰命名法](https://zh.wikipedia.org/zh-cn/%E9%A7%9D%E5%B3%B0%E5%BC%8F%E5%A4%A7%E5%B0%8F%E5%AF%AB)的命名规范。相关介绍可以看[这里](https://zh.wikipedia.org/zh-cn/%E9%A7%9D%E5%B3%B0%E5%BC%8F%E5%A4%A7%E5%B0%8F%E5%AF%AB)

### 控制器

控制器类名需要以`驼峰命名法`进行命名，并且以Controller作为后缀结束。

例如：`UserController`、`TotalStatisticsController`、`InfoListController`

### Model

采用驼峰法，名字可以根据你自己的喜好进行命名。我们会推荐你根据数据库表名或相关的业务用途对model进行命名。

例如：`User`、`Product`、`Price`

### 入参配置文件（`Param/*.ini`）

文件名 **必须** 与你的控制器名称保持一致。每个控制器单独对应一个入参配置文件。

例如：`UserController.ini`、`TotalStatisticsController.ini`、`InfoListController.ini`



## 安全

### 重新生成SessionID

Any time a user has a change in privilege (that is, gaining or losing access
rights within a system) be sure to regenerate the session ID:

```php
<?php
\Yan\Core\Session::regenerateId();
?>
```

> N.b.: The `regenerateId()` method also regenerates the CSRF token value.

### Cross-Site Request Forgery

CSRF（Cross-site request forgery）跨站请求伪造，也被称为“One Click Attack”或者Session Riding，通常缩写为CSRF或者XSRF，是一种对网站的恶意利用。
尽管听起来像跨站脚本（XSS），但它与XSS非常不同，XSS利用站点内的信任用户，而CSRF则通过伪装来自受信任用户的请求来利用受信任的网站。

<http://en.wikipedia.org/wiki/Cross-site_request_forgery>

#### CSRF防御措施

为了防范Csrf攻击，server端的处理逻辑应该是这样的：

1. 为每一个已经登陆了的用户的每个表单请求放置一个验证token；


2. 确保所有通过 POST/PUT/DELETE (i.e., "unsafe") 的请求都包含上一步骤提到的token值。

> N.b.: 如果我们的应用是通过GET请求方法去修改应用资源 (这
> 是一个错误的做法), 我们同样应该对GET方法的请求
> 进行CSRF token的验证，以确保用户资源的安全。

例如，我们想要生成一个CSRF token，可以参考以下做法

```php
<?php
\Yan\Core\Session::getCsrfToken()->getValue();
```

当server端接收到一个请求时，我们应该对CSRF token进行以下的处理:

```php
<?php
use Yan\Core\Session;
use \Yan\Core\Input;

/**
 * @var Vendor\Package\User $user A user-authentication object.
 */
$unsafe = $_SERVER['REQUEST_METHOD'] == 'POST'
       || $_SERVER['REQUEST_METHOD'] == 'PUT'
       || $_SERVER['REQUEST_METHOD'] == 'DELETE';

if ($unsafe && $user->auth->isValid()) {
    $csrf_value = Input::get('__csrf_value');
    $csrf_token = Session::getCsrfToken();
    if (! $csrf_token->isValid($csrf_value)) {
        echo "This looks like a cross-site request forgery.";
    } else {
        echo "This looks like a valid request.";
    }
} else {
    echo "CSRF attacks only affect unsafe requests by authenticated users.";
}
?>
```

## Nginx

我们需要把每一层应用层作为你的根目录
``` bash
# 这里是你的Cgi层
server 
{
    listen       80;
    server_name  cgi.example.com;
    index index.shtml index.html index.htm interface.php;
    root  root  /path/to/root/YanPHP/Application/Cgi/;
     location / {
        try_files $uri $uri/ =404;
        if (!-e $request_filename)
        {
            rewrite (.*) /interface.php;
        }
    }
    location ~ .*\.(php|php5)?$
    {
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
    access_log  /var/log/YanPHP/access.log;
    error_log   /var/log/YanPHP/error.log;
}

# 这里是你的Server层
server 
{
    listen       80;
    server_name  server.example.com;
    index index.shtml index.html index.htm interface.php;
    root  root  /path/to/root/YanPHP/Application/Server/;
     location / {
        try_files $uri $uri/ =404;
        if (!-e $request_filename)
        {
            rewrite (.*) /interface.php;
        }
    }
    location ~ .*\.(php|php5)?$
    {
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
    access_log  /var/log/YanPHP/access.log;
    error_log   /var/log/YanPHP/error.log;
}
```

## Apache

如果你需要进行url重写，那么你需要开启 `rewrite module`。
YanPHP已经为你编写好了 `.htaccess` 文件，并且存放在每一个应用层的目录下。

``` apacheconfig
<VirtualHost *:80>
    DocumentRoot "/path/to/root/YanPHP/Application/Cgi/;"
    ServerName cgi.example.com
    AddType application/x-httpd-php .php
    <Directory />
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
        DirectoryIndex interface.php
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot "/path/to/root/YanPHP/Application/Server/;"
    ServerName server.example.com
    AddType application/x-httpd-php .php
    <Directory />
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
        DirectoryIndex interface.php
    </Directory>
</VirtualHost>
```

## Tests

YanPHP编写了相关的测试用例，可以通过运行一下命令进行单元测试，同时也欢迎提交pull request进行测试用例的补充。
``` bash
phpunit --configuration phpunit.xml
```

## License

MIT