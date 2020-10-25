# DI服务汇总

## DI服务初始化
全部依赖注入的资源服务，都位于```./config/di.php```文件内。  

### 基本注册

默认情况下，会进行基本注册如下：  

```
$di = \PhalApi\DI();

// 配置
$di->config = new FileConfig(API_ROOT . '/config');

// 调试模式，$_GET['__debug__']可自行改名
$di->debug = !empty($_GET['__debug__']) ? true : $di->config->get('sys.debug');

// 日记纪录
$di->logger = new FileLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);

// 数据操作 - 基于NotORM
$di->notorm = new NotORMDatabase($di->config->get('dbs'), $di->debug);
```

### 定制注册

可以根据项目的需要，进行定制化的注册，只需要把下面的注释去掉即可。  

```
// 签名验证服务
// $di->filter = new \PhalApi\Filter\SimpleMD5Filter();

// 缓存 - Memcache/Memcached
// $di->cache = function () {
//     return new \PhalApi\Cache\MemcacheCache(DI()->config->get('sys.mc'));
// };

// 支持JsonP的返回
// if (!empty($_GET['callback'])) {
//     $di->response = new \PhalApi\Response\JsonpResponse($_GET['callback']);
// }
```

如果需要更多的DI服务，也可以参考并使用下面的DI服务资源一览表。  

## DI服务资源一览表

假设，我们已有：  
```
$di = \PhalApi\DI();
```

则：  

服务名称|是否启动时自动注册|是否必须|接口/类|作用说明
---|---|---|---|---
$di->config|否|是|[PhalApi\Config](https://github.com/phalapi/kernal/blob/master/src/Config.php)|配置：负责项目配置的读取，需要手动注册，指定存储媒介，默认是[PhalApi\Config\FileCache](https://github.com/phalapi/kernal/blob/master/src/Cache/FileCache.php)
$di->logger|否|是|[PhalApi\Logger](https://github.com/phalapi/kernal/blob/master/src/Logger.php)|日记纪录：负责日记的写入，需要手动注册，指定日记级别和存储媒介，默认是[PhalApi\Logger\FileLogger](https://github.com/phalapi/kernal/blob/master/src/Logger/FileLogger.php)
$di->request|是|是|[PhalApi\Request](https://github.com/phalapi/kernal/blob/master/src/Request.php)|接口参数请求：用于收集接口请求的参数
$di->response|是|是|[PhalApi\Response](https://github.com/phalapi/kernal/blob/master/src/Response.php)|结果响应：用于输出返回给客户端的结果，默认为[PhalApi\Response\JsonResponse](https://github.com/phalapi/kernal/blob/master/src/Response/JsonResponse.php)
$di->notorm|否|推荐|[PhalApi\Database\NotORMDatabase](https://github.com/phalapi/kernal/blob/master/src/Database/NotORMDatabase.php)|数据操作：基于NotORM的DB操作，需要手动注册，指定数据库配置
$di->cache|否|推荐|[PhalApi\Cache](https://github.com/phalapi/kernal/blob/master/src/Cache.php)|缓存：实现缓存读写，需要手动注册，指定缓存
$di->filter|否|推荐|[PhalApi\Filter](https://github.com/phalapi/kernal/blob/master/src/Filter.php)|拦截器：实现签名验证、权限控制等操作
$di->crypt|否|否|[PhalApi\Crypt](https://github.com/phalapi/kernal/blob/master/src/Crypt.php)|对称加密：实现对称加密和解密，需要手动注册
$di->curl|否|否|[PhalApi\CUrl](https://github.com/phalapi/kernal/blob/master/src/CUrl.php)|CURL请求类：通过curl实现的快捷方便的接口请求类，需要手动注册
$di->cookie|否|否|[PhalApi\Cookie](https://github.com/phalapi/kernal/blob/master/src/Cookie.php)|COOKIE的操作
$di->tracer|是|是|[PhalApi\Helper\Tracer](https://github.com/phalapi/kernal/blob/master/src/Helper/Tracer.php)|内置的全球追踪器，支持自定义节点标识  
$di->debug|否|否|boolean|应用级的调试开关，通常可从配置读取，为true时开启调试模式
$di->admin|是|是|[Portal\Common\Admin](https://github.com/phalapi/phalapi/blob/master-2x/src/portal/Common/Admin.php)|Portal运营平台登录的管理员会话

## DI服务是否已注册的判断误区

### (1)错误的判断方法

当需要判断一个DI服务是否已被注册，出于常识会这样判断：  
```php
if (isset(\PhalApi\DI()->cache)) {
```
但这样的判断永远为false，不管注册与否。  
  
追其原因在于，DI类使用了魔法方法的方式来提供类成员属性，并存放于```PhalApi\DependenceInjection::$data```中。  
  
这就导致了如果直接使用isset(\PhalApi\DI()->cache)的话，首先不会触发魔法方法 ```PhalApi\DependenceInjection::__get($name)```的调用，其次也确实没有```PhalApi\DependenceInjection::$cache``` 这个成员属性，最终判断是否存在时都为false。  
  
简单来说，以下两种判断，永远都为false：  
```php
$di = \PhalApi\DI();

// 永远为false
var_dump(isset($di->XXX));
var_dump(!empty($di->XXX));
```

### (2)正确判断的写法：先获取，再判断

正确的用法应该是：  
```php
// 先获取，再判断
$XXX = $di->XXX;
var_dump(isset($XXX));
var_dump(!empty($XXX));
```  
 
