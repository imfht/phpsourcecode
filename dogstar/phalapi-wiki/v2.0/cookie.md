# COOKIE

当使用HTTP/HTTPS协议并需要使用COOKIE时，可参考此部分的使用说明。  

## COOKIE的基本使用

如同其他的服务一样，我们在使用前需要对COOKIE进行注册。COOKIE服务注册在```\PhalApi\DI()->cookie```中，可以使用[PhalApi\Cookie](https://github.com/phalapi/kernal/blob/master/src/Cookie.php)实例进行初始化，如：  
```php
$config = array('domain' => '.phalapi.net');
\PhalApi\DI()->cookie = new PhalApi\Cookie($config);
```
其中，```PhalApi\Cookie```的构造函数是一个配置数组，上面指定了Cookie的有效域名/子域名。其他的选项还有：  


配置选项|说明|默认值
---|---|---
path|Cookie有效的服务器路径|NULL
domain|Cookie的有效域名/子域名|NULL
secure|是否仅仅通过安全的HTTPS连接传给客户端|FALSE
httponly|是否仅可通过HTTP协议访问|FALSE

注册COOKIE服务后，便可以开始在项目中使用了。COOKIE的使用主要有三种操作，分别是：设置COOKIE、获取COOKIE、删除COOKIE。下面是一些简单的使用示例。  
```php
// 设置COOKIE
// Set-Cookie:"name=phalapi; expires=Sun, 07-May-2017 03:26:45 GMT; domain=.phalapi.net"
\PhalApi\DI()->cookie->set('name', 'phalapi', $_SERVER['REQUEST_TIME'] + 600);

// 获取COOKIE，输出：phalapi
echo \PhalApi\DI()->cookie->get('name');

// 删除COOKIE
\PhalApi\DI()->cookie->delete('name');
```

## 记忆加密升级版

实际情况，项目对于COOKIE的使用情况更为复杂。比如，需要对数据进行加解密，或者需要突破COOKIE设置后下一次请求才能生效的限制。为此，PhalApi提供一个升级版的COOKIE服务。其特点主要有：  

 + 1、对COOKIE进行加密输出、解密获取  
 + 2、自带记忆功能，即本次请求设置COOKIE后便可直接获取    

当需要使用这个升级版COOKIE替代简单版COOKIE服务时，可使用[PhalApi\Cookie\MultiCookie](https://github.com/phalapi/kernal/blob/master/src/Cookie/MultiCookie.php)实例进行重新注册。在初始化时，```PhalApi\Cookie\MultiCookie```构建函数的第一个参数配置数组，除了上面简单版的配置项外，还有：  

配置选项|说明|默认值
---|---|---
crypt|加解密服务，须实现PhalApi\Crypt接口|\PhalApi\DI()->crypt
key|crypt使用的密钥|debcf37743b7c835ba367548f07aadc3

假设项目中简单地使用base64对COOKIE进行加解密，则可先添加加解密服务的实现类。  
```php
<?php
namespace App\Common\Crypt\Base64Crypt;

use PhalApi\Crypt;

class Base64Crypt implements Crypt {

    public function encrypt($data, $key) {
        return base64_encode($data);
    }

    public function decrypt($data, $key) {
        return base64_decode($data);
    }
}
```

随后，在文件```./config/di.php```使用该加解密实现类重新注册```\PhalApi\DI()->cookie```服务，由于加解密中未使用到密钥```$key```，所以可以不用配置。  
```php
$config = array('domain' => '.phalapi.net', 'crypt' => new App\Common\Crypt\Base64Crypt());
$di->cookie = new PhalApi\Cookie\Multi($config);
```

最后，便可在项目中像简单版原来那样使用升级版的COOKIE服务了，但设置的COOKIE值则是经过加密后的。  

```
// 设置COOKIE
// Set-Cookie:"name=cGhhbGFwaQ%3D%3D; expires=Sun, 07-May-2017 03:27:57 GMT; domain=.phalapi.net"
\PhalApi\DI()->cookie->set('name', 'phalapi', $_SERVER['REQUEST_TIME'] + 600);
```

此外，在同一次请求中，设置了某个COOKIE后，也可以“即时”获取了。  

在使用COOKIE时，需要注意：  

 + 1、敏感数据不要存到COOKIE，以保证数据安全性
 + 2、尽量不要在COOKIE存放过多数据，避免产生不必要的流量消耗  

## 扩展：定制专属的COOKIE

当项目中需要定制专属的COOKIE服务时，可以继承[PhalApi\Cookie](https://github.com/phalapi/kernal/blob/master/src/Cookie.php)基类，并按需要重写对应的接口。主要的接口有三个：  

 + 设置COOKIE：```PhalApi\Cookie::set($name, $value, $expire = NULL)```  
 + 获取COOKIE：```PhalApi\Cookie::get($name = NULL)```  
 + 删除COOKIE：```PhalApi\Cookie::delete($name)```

值得注意的是，在实现子类的构造函数中，需要调用PhalApi_Cookie基类的构造方法，以便初始化配置选项。实现子类后，重新注册便可使用，这里不再赘述。   