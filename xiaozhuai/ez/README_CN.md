# EZ Framework for PHP

[[中文文档](README_CN.md)] [[English README](README.md)]

![logo](logo.png)

* author: xiaozhuai

* email: 798047000@qq.com

# 关于

EZ是一个php mvc框架，它轻量，美丽而性感。

为什么使用EZ？ 因为它EASY！ :)

# 维基

这里是一个使用EZ搭建的应用示例，你可以参照这个项目来使用EZ。

oschina: [http://git.oschina.net/xiaozhuai/ez_app](http://git.oschina.net/xiaozhuai/ez_app)

github: [https://github.com/xiaozhuai/ez_app](https://github.com/xiaozhuai/ez_app)

## Hello World

让我们从最简单的Hello World开始

**1.** 创建一个apache `.htaccess` 文件

```.htaccess
<IfModule mod_rewrite.c>
	RewriteEngine On
	#ignore if it's a file
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule .* index.php
</IfModule>
```

这个文件只是一个重写规则，让所有的请求经过index.php来处理，除了静态文件

你可以找到其它环境下的配置，像nginx什么的，google it.

**2.** 克隆EZ到目录 `./vendor/ez`

**3.** 在项目根目录创建一个入口文件 `index.php` (例如/var/www 或者别的什么)

```php
require_once __DIR__ . "/vendor/ez/autoload.php";       //include ez autoload
EZ()->init(__DIR__);                                    //init framework in cur dir
EZ()->mvc(                                              //define the mvc path
    __DIR__ . "/models",
    __DIR__ . "/views_php",
    __DIR__ . "/controllers"
);
EZ()->run();                                            //let's go
```
**4.** 创建 `models`, `views_php`, `controllers` 这三个文件夹

**5.** 在 `controllers` 目录下创建一个Controller `IndexController.php`

```php
class IndexController extends EZController      //all Controller must extends EZController
{
    public function index(){
        echo "Hello world";
    }
}
```

完成了， 访问 [http://localhost/](http://localhost/) 试试吧 :)

## 配置 和 函数

Hello World 很简单不是吗？来看一些高级货。

找到 ***ez_app*** 项目中的 `index.php`

```php
require_once __DIR__ . "/vendor/ez/autoload.php";
EZ()->session(true);                                    //whether enable session support
EZ()->init(__DIR__);                                    //init framework in cur dir
EZ()->errView(__DIR__ . "/views_php/err.phtml");        //define the err page view tpl
EZ()->config(                                           //load configs
    include __DIR__ . "/conf/main.config.php",
    include __DIR__ . "/conf/db.config.php",
    include __DIR__ . "/conf/php_view_engine.config.php"
);
EZ()->mvc(                                              //define the mvc path
    __DIR__ . "/models",
    __DIR__ . "/views_php",
    __DIR__ . "/controllers"
);
EZ()->library(__DIR__ . "/lib");                        //define the user class lib, will be automatically included
EZ()->preRun(function(){
    assert(1+1==2);                                     //do something before run
});
EZ()->run();                                            //let's go
```

正如你看到的，几乎所有的调用都是形似 `EZ()->member`, 或 `EZXX()->member` 这样的形式

这里有一个列表，你可以在 `EZFunctions.php` 找到它们

| Function   | Info    
| ---        | ---     
| EZ()       | Main framework      
| EZRouter() | Router behave
| EZView()   | View behave
| EZConfig() | Configs
| EZGlobal() | Global vars

所有这个函数(方法)都会返回一个实例，它们都是单例对象。

对于配置文件，一个合法的配置文件内容如下

```php
return array(
    "WEB_ROOT"                        => "/",
    "VIEW_ACTION_HYPHEN"              => "/",
);
```

**注意！** 字母全部大写的配置项是内置的，如果你想扩展一些配置一遍在应用中使用的话，请使用小写字母以区分。

你可以像例子中一样同时加载多个配置文件

所有内置的配置项如下， 或者你可以在 `EZConfig.php` 中找到

```php

/**
 * define the static path pf the project, change this by call EZ()->init()
 */
public $PROJECT_PATH                    = "";

/**
 * define the root of the project, for example "/var/www/myproject",
 * then it should be set to "/myproject"
 */
public $WEB_ROOT                        = "/";

/**
 * define the ext name of view
 */
public $VIEW_EXT                        = "phtml";

/**
 * define the hyphen of view, for example, a controller "Home" has an action called "index", if set this to '.', then
 * view file name should be Home{$VIEW_ACTION_HYPHEN}index.{$VIEW_EXT} (Home.index.phtml).
 * if you want to put all action view under a child dir, then you can just set this to '/'.
 */
public $VIEW_ACTION_HYPHEN              = "/";

/**
 * define the view engine of view renderer, avaliable engines: php, smarty, twig, haml_php, haml_twig
 */
public $VIEW_ENGINE                     = "php";

public $PDO_DB_DSN                      = "";

public $PDO_DB_USER                     = "root";

public $PDO_DB_PWD                      = "";

public $PDO_DB_OPTIONS                  = array();

public $MONGO_DSN                       = "";

public $MONGO_DBNAME                    = "";

```

由于EZConfig是单例，所以它是全局的。

如果你想要存储一些全局变量，我建议你使用 `EZGlobal` 而不是 `EZConfig`，当然这并不是强制的，仅仅是为了区分而已，
同样，请使用小写。

## 路由 和 控制器

### 內建路由

EZ框架的路由行为类似于其它一些框架，像 `ThinkPHP`， `yaf` 等。

但不是完全相同，至少它很EZ。

EZRouter 将会匹配Controller目录下的类和方法作为控制器和行为

有 ***3*** 种情景

**1.** 对于url file path 是 `/`

它总是会route到 `IndexController` 类的 `index` 方法。
其文件名为 `IndexController.php`， 位于 `view_path` 的根目录下 ( view_path 是通过 EZ()->mvc(model_path, view_path, controller_path) 设置的 )

`IndexController` 类必须只能包含一个行为(action 而不是 method，action 是一个 method，但method不一定是 action)
例如该类有一个名为 `method` 的方法，这时访问 `/method` 并不会route到 `IndexController::method`


**2.** 对于 url file path 是 `/aaa/bbb/ccc`

首先他会在 `view_path/aaa/bbb` 目录下查找名为 `ccc` (或者是`Ccc`，忽略大小写的) 的类，如果存在，
则行为为默认行为 `index`，所以会route到`ccc::index`，如果没有 `index` 方法，会抛出一个action不存在异常。

如果 `ccc` 类不存在，那么会去 `view_path/aaa` 目录下查找 `bbb` 类，如果存在，
那么行为为 `ccc`, 所以会route到 `bbb::ccc`，如果没有 `ccc` 方法，同样会抛出一个action不存在的异常。

如果以上情况都不成立，会抛出一个controller不存在的异常。

**3.** 当然，这里其实并没有第三种情形 :)

### 自定义路由

你可以制定一个如下的路由规则：

```php
return array(
    "user/:id"                      => "user",
    "user/info/:name/:age/:sex"     => "user/info"
);
```

具体可查看 `index.php` 和 `controllers/UserController.php` 中的例子.
EZ将会从下至上匹配这些规则，直到命中，这意味着如果一个uri匹配多条规则，则实际是最后一条生效。

## 模型

EZ支持pdo(mysql, sqlite, 等等) 和 mongodb，你可以自己扩展更多的model，通过继承 `EZModel` 类

为什么不支持orm？因为我不喜欢orm， :) 性能很低下不是吗，当然如果需要的话，那就自己扩展吧。

## 视图 和 视图引擎

EZ支持一些流行的视图引擎，而且你也可以自己扩展更多的视图引擎，所有这一切都是EZ的

内置的视图引擎有 `php`, `smarty`, `twig`, `haml_php`, `haml_twig`。

`php` 视图引擎是默认开始的视图引擎，因为php是最好的模板语言，不是吗？

你可以参照 `ez_app` 项目来使用这些视图引擎
直接编辑 `.htaccess` 中的 `RewriteRule .* index.php` 为 `RewriteRule .* index_smarty.php` 来使用smarty。
当然这需要smarty库的依赖支持，项目中并未包含，你需要自己下载。

或者你可以扩展其它视图引擎，参照例子 `index_php_markdown.php`.

这是所有你需要做的事情来扩展一个支持markdown和php混合的视图引擎

```php
require_once __DIR__ . "/vendor/ez/autoload.php";
EZ()->session(true);                                    //if enable session support
EZ()->init(__DIR__);                                    //init framework in cur dir
EZ()->config(                                           //load configs
    include __DIR__ . "/conf/main.config.php",
    include __DIR__ . "/conf/db.config.php",
    include __DIR__ . "/conf/php_markdown_view_engine.config.php"
);
EZ()->mvc(                                              //define the mvc path
    __DIR__ . "/models",
    __DIR__ . "/views_php_markdown",
    __DIR__ . "/controllers"
);
EZ()->library(__DIR__ . "/lib");                        //define the user class lib, will be automatically included
EZ()->viewEngine(new Parsedown());                      //the view engine instance

/**
 * $engine is set by EZ()->viewEngine($parsedown);
 * $vars
 * $path is realpath path (static path) of view tpl file
 */
EZ()->registerViewEngine("php_markdown", function ($engine, $vars, $path){
    /**
     * first, render tpl just like phtml
     */
    ob_start();
    foreach ($vars as $key => $value)
        ${$key} = $value;
    require_once $path;
    $markdown = ob_get_contents();
    ob_clean();

    /**
     * then, use Parsedown to transfer markdown to html
     */
    echo $engine->text($markdown);

});
EZ()->run();                                            //let's go
```

EZ? So enjoy! :)