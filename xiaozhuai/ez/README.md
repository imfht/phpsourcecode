# EZ Framework for PHP

[[中文文档](README_CN.md)] [[English README](README.md)]

![logo](logo.png)

* author: xiaozhuai

* email: 798047000@qq.com

# About

EZ is a MVC framework for php, it tiny, beauty, ans sexy.

Why EZ? Because it's EASY!  :)

# Wiki

Here it an example app use EZ framework, you can just follow this step by step. go

oschina: [http://git.oschina.net/xiaozhuai/ez_app](http://git.oschina.net/xiaozhuai/ez_app)

github: [https://github.com/xiaozhuai/ez_app](https://github.com/xiaozhuai/ez_app)

Or continue reading ...

## Hello World

Let's start with a "Hello World".

**1.** create an apache `.htaccess` file

```.htaccess
<IfModule mod_rewrite.c>
	RewriteEngine On
	#ignore if it's a file
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule .* index.php
</IfModule>
```

this file just rewrite all request to use index.php to handle(except static files),

you can find other server's config file, such as nginx, etc by google it.

**2.** clone ez to `./vendor/ez`

**3.** create an entry `index.php` under the root of the web (/var/www or something else)

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
**4.** create `models`, `views_php`, and `controllers` dir

**5.** create a php file `IndexController.php` under `controllers`

```php
class IndexController extends EZController      //all Controller must extends EZController
{
    public function index(){
        echo "Hello world";
    }
}
```

We've already got it! Visit [http://localhost/](http://localhost/) :)

## Configs & Functions

Hello World is EZ, yes? Let's do something more.

Look at `index.php` in ***ez_app*** project

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

As you see, most of the thing you do with EZ will call it like `EZ()->member`, or `EZXX()->member`

Here it's a list, or you can find it in `EZFunctions.php`

| Function   | Info    
| ---        | ---     
| EZ()       | Main framework      
| EZRouter() | Router behave
| EZView()   | View behave
| EZConfig() | Configs
| EZGlobal() | Global vars

All of these instance is singleton

For configs, a valid config file look like this

```php
return array(
    "WEB_ROOT"                        => "/",
    "VIEW_ACTION_HYPHEN"              => "/",
);
```

**WARNING!** Configs with capital letter are built in, 
if you want to extend some config to use in your application, use lowercase.

You can load multi config files.

All built in configs here, and you can find it in `EZConfig.php`

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

For EZConfig is singleton, so it's global, right?

If you want storge some global temp vars, I suggest you use the `EZGlobal` not `EZConfig`,
also, use lowercase.

## Router & Controller

### Built-in router

For EZ, route behave like some others framework, such as `ThinkPHP`, `yaf`, etc.

But not all the same, at least, it's EZ!

EZRouter will find matched `Class` and `Method`

Here is ***3*** situations

**1.** For url file path `/`

it will always route to class `IndexController`, method `index`, 
which class file named `IndexController.php` in `view_path`( set by EZ()->mvc(model_path, view_path, controller_path) )

`IndexController` class must only container one action method named `index`, 
for example, there is a method named `method`, if you visit `/method`, it will not route to `IndexController::method`


**2.** For url file path `/aaa/bbb/ccc`

it will find controller class `ccc`(or `Ccc`, anyway, it will ignore case) in `view_path/aaa/bbb`, if exist,
the action method will be `index` by default, so it will route to `ccc::index`

if `ccc` not exist, it will find controller class `bbb` in `view_path/aaa`, if exist,
the action method will be `ccc`, so it will route to `bbb::ccc`

else it will throw an exception

**3.** Of course, there is no third situation, it will throw an exception, yes？ :)

### Custom router

Write a custom route config like this :

```php
return array(
    "user/:id"                      => "user",
    "user/info/:name/:age/:sex"     => "user/info"
);
```

Follow the example in `index.php` and `controllers/UserController.php`.
EZ will match will rules from bottom to to, which means the last rule will be effective.


## Model

EZ support pdo(mysql, sqlite, etc) and mongodb, and you can extend your model by extend class EZModel.

Why not orm? It's awful, isn't it? You can add orm support yourself.

## View & View Engine

EZ support some popular view engine, and you can simply extend others view engine.

Built in view engine are `php`, `smarty`, `twig`, `haml_php`, `haml_twig`.

`php` is used by default, as php is the best tpl language, right?

You can follow examples in `ez_app` to use these view engine, 
for example, just edit `.htaccess` line `RewriteRule .* index.php` to `RewriteRule .* index_smarty.php` to use smarty.
It require smarty dependence which it's not included.

Or you can extend others view engine follow example in `index_php_markdown.php`.

Here is all to need to do to extend a view engine for markdown

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