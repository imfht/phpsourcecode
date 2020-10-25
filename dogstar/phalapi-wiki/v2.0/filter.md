# 过滤器

  
## 默认可用的MD5签名

基于很多同学对接口签名验证比较陌生，PhalApi提供了一个基本版的接口验证服务。主要是基于md5进行的签名生成，这个只能作为一般性的参考。大家可以在此基础上进行调整延伸。  
  
默认情况下，在```./config/di.php```文件中去掉注释便可开启此接口验证，即：  
```php
// 签名验证服务
$di->filter = new \PhalApi\Filter\SimpleMD5Filter();
```  
  
其验签的算法如下（如注释所示）：  
```php
1、排除签名参数（默认是sign）
2、将剩下的全部参数，按参数名字进行字典排序
3、将排序好的参数，全部用字符串拼接起来
4、进行md5运算
```  
    
以下面的示例参数为例，即：  
```
1、排除签名参数（默认是sign）
?service=Examples_CURD.Get&id=1

2、将剩下的全部参数，按参数名字进行字典排序
id=1
service=Examples_CURD.Get

3、将排序好的参数，全部用字符串拼接起来
"1Examples_CURD.Get" = "1" + "Examples_CURD.Get"

4、进行md5运算
sign = 3ba5f5f03a90b2a648f5dd1df7387e26 = md5("1Examples_CURD.Get")

5、请求时，加上签名参数
?service=Examples_CURD.Get&id=1&sign=3ba5f5f03a90b2a648f5dd1df7387e26
```
  
下面是两个调用示例，错误请求下（即签名失败）：  
```
http://dev.phalapi.net/?service=Examples_CURD.Get&id=1&sign=xxx

返回：
{
    "ret": 406,
    "data": [],
    "msg": "非法请求：签名错误"
}
```

> 温馨提示：签名错误情况下，可以查看日记获得正确的sign，如：  
> ```2017-07-22 12:02:18|DEBUG|Wrong Sign|{"needSign":"3ba5f5f03a90b2a648f5dd1df7387e26"}```
  

正常请求下（带sign签名）：  
```
http://dev.phalapi.net/?service=Examples_CURD.Get&id=1&sign=3ba5f5f03a90b2a648f5dd1df7387e26
```
  
如果不想使用sign作为关键的签名参数，可以在注册时指定，如使用缩写s：  
```php
$di->filter = new \PhalApi\Filter\SimpleMD5Filter('s');
```

## 白名单配置

对于不需要进行签名验证的接口服务，可以使用白名单配置，通过框架自身实现对指定配置的接口服务排除。即调用的接口服务，如果配置了白名单，则不调用过滤器。  
  
接口服务白名单配置是：```app.service_whitelist```，即配置文件```./config/app.php```里面的```service_whitelist```配置，其默认值是：  
```php
    'service_whitelist' => array(
        'Site.Index',
    ),
```
如源代码里的注释所示，配置的格式有以下四种。  

类型|配置格式|匹配规则|示例及说明  
---|---|---|---  
全部|```*.*```|匹配全部接口服务（慎用！）|如果配置了此规则，即全部的接口服务都不触发过滤器。  
方法通配|```Site.*```|匹配某个类的任何方法|即App\Api\Site接口类的全部方法  
类通配|```*.Index```|匹配全部接口类的某个方法|即全部接口类的Index方法  
具体匹配|```Site.Index```|匹配指定某个接口服务|即App\Api\Site::Index()  
  
如果有多个生效的规则，按短路判断原则，即有任何一个白名单规则匹配后就跳过验证，不触发过滤器。  
  
以下是更多的示例：  
```php
    'service_whitelist' => array(
        '*.Index',           // 全部的Index方法
        'Test.*',            // Api_Test的全部方法
        'User.GetBaseInfo',  // Api_User::GetBaseInfo()方法
    ),
```

配置好上面的白名单后，以下这些接口服务全部不会触发过滤器：  
```
// 全部的Index方法
?service=Site.Index
?service=User.Index

// Api_Test的全部方法
?service=Test.DoSth
?service=Test.Hello
?service=Test.GOGOGO

// Api_User::GetBaseInfo()方法
?service=User.GetBaseInfo
```

#### 命名空间白名单独立配置

如果需要为不同的命名空间独立配置白名单，只需要简单加多一层配置即可，即单独配置的路径是：  
```
app.service_whitelist.{命名空间}
```

对应的配置示例是： 
```php
    'service_whitelist' => array(
        'Site.Index',

        // 以命名空间名称为key
        'App' => array(
            // 在这里，单独配置……
        ),
    ),
```

### 更好地建议

通常关于接口签名这块，我们还需要：  

 + 1、为不同的接入方定义不同的密钥和私钥；
 + 2、如果业务需要，为各个接口、各个接入方分配调用权限；
 + 3、统一签名参数的规则，可以配置在./config/app.php中的，如上面的签名需要的参数，我们可以追加统一的参数规则：
```php
    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        'signature' => array('name' => 'signature', 'require' => true),
        'timestamp' => array('name' => 'timestamp', 'require' => true),
        'nonce' => array('name' => 'nonce', 'require' => true),
    ),
```

## 扩展：实现你的签名方式

如果我们需要实现签名验证，只需要简单的两步即可：  

 + 1、实现过滤器接口 **PhalApi\Filter::check()**；
 + 2、注册过滤器服务 **PhalApi\DI()->filter**；
   

下面以大家熟悉的 [微信验签](http://mp.weixin.qq.com/wiki/17/2d4265491f12608cd170a95559800f2d.html) 为例，进行示例说明。

### 实现过滤器接口

通常我们约定返回ret = 402表示验证失败，所以当签名失败时，我们可以返回ret = 402以告知客户端签名不对。根据微信的检验signature的PHP示例代码，我们可以快速实现自定义签名规则，如：
```php
// 文件 ./src/app/Common/SignFilter.php 
<?php
namespace App\Common;

use PhalApi\Filter;
use PhalApi\Exception\BadRequestException;

class SignFilter implements Filter
{
    public function check()
    {
        $signature = \PhalApi\DI()->request->get('signature');
        $timestamp = \PhalApi\DI()->request->get('timestamp');
        $nonce = \PhalApi\DI()->request->get('nonce');  

        $token = 'Your Token Here ...';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if ($tmpStr != $signature) {
            throw new BadRequestException('wrong sign', 1);
        }
    }
}
```

### 注册过滤器服务

随后，我们只需要再简单地注册一下过滤器服务即可，在```./config/di.php```文件最后追加：

```php
// 签名验证服务
$di->filter = new App\Common\SignFilter();
```
