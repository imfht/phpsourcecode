# 一个简单性感的php router



## Install
```
composer require man0sions/router

```
## Run Demo
```
 1:git clone https://git.oschina.net/man0sions/Router.git
 2:php -S 127.0.0.1:8080 public/index.php 
 3:在浏览器访问: http://localhost:8080/home
 
```
## useage

### 1:简单用法
```

$router = \LuciferP\Router\Base\RouterFactory::getRouter();


/**
 * 1:简单用法
 * $res->status() 设置返回码[默认200] 200,404,500 ...
 * $res->type()   设置返回类型[默认 text/html] text/json...
 * $res->json()   在页面输出json
 * $res->jsonp()  在页面输出jsonp
 * $res->render() 把数据渲染到指定的页面
 */

$router->get('/home', function ($req, $res) {

//  $res->status(200)->send(json_encode($req));
//  $res->type('text/json')->send(json_encode($req));
//  $res->json(['hello'=>'world']);
//  $res->jsonp(['hello'=>'world']);
//  $res->redirect("http://baidu.com");
    $res->status(200)->type('text/html')->render(BASE_PATH . "/../views/view.php", ['name' => 'zhangsan', 'age' => 20]);
});

$router->run();


```

### 1.1 参数解析
```
/**
 * 1.1: get参数
 */

$router->get('/hello/:name', function ($req, $res) {
    $query = $req['get'];
    $res->json($query);
});

/**
 * 1.2 post参数
 */

$router->post('/hello', function ($req, $res) {
    $query = $req['post'];
    $res->json($query);
});

```
### 2:高级用法

```

/**
 * 2.1 auth
 * 用户名密码默认为:admin,admin
 */
$router->auth("/auth", function ($req, $res) {
    $name = @$req['server']['PHP_AUTH_USER'];
    $passwd = @$req['server']['PHP_AUTH_PW'];

    if (!($name == 'admin' && $passwd == 'admin')) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    } else {
        $res->send("欢迎回来");

    }

});
/**
 * 2.2 格式化response
 * html---\LuciferP\Router\ResponseData\HtmlData
 * json---\LuciferP\Router\ResponseData\JsonData
 * xml----\LuciferP\Router\ResponseData\XmlData
 */
$router->get('/name/:name/age/:age', function ($req, $res) {
    $query = $req['get'];
    $xml = $res->dataformat(new \LuciferP\Router\ResponseData\XmlData($query));
    $res->type("text/xml")->send($xml);
});


/**
 * 2.3 指定所有[get,post]请求"/"
 */
$router->all("/", function ($req, $res) {
    $res->send("all page");
});



```
### 3. controller 使用方法

```
/**
 * 3.1 :只渲染包含layout 的 html
 */
$router->get('/home/index', '\LuciferP\Router\Controller\Home@index');

/**
 * 3.2 :调用response渲染数据
 */
$router->get('/home/index2', '\LuciferP\Router\Controller\Home@index2');

/**
 * 3.3 :把包含在layout 的 html一起渲染的数据交给response返回
 */
$router->get('/home/index3', '\LuciferP\Router\Controller\Home@index3');


```


### 4.最后别忘了加上 $router->run();

```
$router->run();

```