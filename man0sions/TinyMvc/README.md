# PHP Tiny MVC Framework

* 自己开发的简约高性能php mvc框架，包含：路由，ORM，session，view，cache等等。
* 支持Mysql && sqlite


## 截图
![image](http://101.200.130.104/images/YH/tinymvc1.png)
![image](http://101.200.130.104/images/YH/tinymvc2.png)

## 目录结构

```
TinyMvc/

├── config                              
│   └── main.php    //配置文件
├── demo                               
│   └── users.sql   //demo 数据库
├── public                              
│   ├── index.php   //app 入口
│   └── static      //静态资源
├── runtime
│   ├── cache       //文件缓存
│   └── logs        //日志

├── src  //源文件
│   ├── app
│   │   ├── Application.php   
│   │   └── ApplicationHelper.php
│   ├── base
│   │   ├── AppException.php
│   │   ├── Config.php
│   │   ├── Decorators.php
│   │   └── TinyMvc.php
│   ├── controllers             //controller 目录
│   │   ├── BaseController.php
│   │   ├── Home.php
│   │   └── Users.php
│   ├── models                  //models 目录
│   │   └── User.php
│   ├── routers                 //路由目录
│   │   ├── default.php
│   │   └── users.php
│   └── utils
│       └── Utils.php
└── views                       //views目录
    ├── error
    │   └── error.php
    ├── home
    │   └── index.php
    ├── layouts
    │   └── main.php
    └── users
```
## 系统环境

* Composer
* PHP 5.6+
* PDO extension

## install 

```
git clone https://git.oschina.net/man0sions/TinyMvc.git
cd TinyMvc
composer update

配置 {PATH}/TinyMvc/public 为apache/nginx 根目录,并开启目录重写

```


## 配置文件

```
参照 : config/main.php
```

### 路由配置
>src/routers
>详细路由配置请查看 http://git.oschina.net/man0sions/Router


```
$router->get("/","Home@index");

$router->get("/users","Users@index");


$router->get("/users/id/:id","Users@view");

```

### controller 
> src/controllers
> 详细controller用法查看 http://git.oschina.net/man0sions/Controller

```
class Users extends BaseController
{
    private $page_size = 10;

   
    public function index()
    {
        $users = User::model()->limit($start, $this->page_size)->findAll();
        return $this->render(['users' => $users]);
    }
    
}
```

### view 

> views

```
默认情况下 controller
Home@index
对应 views
home/index.php

```


### model
> src/models
> 详细用法查看 http://git.oschina.net/man0sions/Orm

```
use LuciferP\Orm\base\Model;

class User extends Model
{
    protected $table="users";
}
```




## demo
```
demo 数据库在 demo/users.sql

展示了 user curd的实现细节,包括:
路由配置 src/routers/users.php
controller配置  src/controllers/Users.php
view views/users


```

