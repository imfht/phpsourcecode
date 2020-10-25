# Api接口服务层

**Api接口层**称为接口服务层，负责对客户端的请求进行响应，处理接收客户端传递的参数，进行高层决策并对领域业务层进行调度，最后将处理结果返回给客户端。  

## 接口参数规则配置

接口参数，对于接口服务本身来说，是非常重要的。对于外部调用的客户端来说，同等重要。对于接口参数，我们希望能够既减轻后台开发对接口参数获取、判断、验证、文档编写的痛苦；又能方便客户端快速调用，明确参数的意义。由此，我们引入了**参数规则**这一概念，即：通过配置参数的规则，自动实现对参数的获取和验证，同时自动生成在线接口文档。  

### 一个简单的示例
假设我们现在需要提供一个用户登录的接口，接口参数有用户名和密码，那么新增的接口类和规则如下：  
```php
// 文件 ./src/app/Api/User.php
<?php
namespace App\Api;

use PhalApi\Api;

class User extends Api {

    /**
     * 接口参数规则配置
     */
    public function getRules() {
        return array(
            'login' => array( // login对应接口方法名，不区分大小写
                'username' => array('name' => 'username'), // 键对应接口类成员属性名称，值为参数规则配置数组
                'password' => array('name' => 'password'),
            ),
        );
    }

    public function login() {
        $name = $this->username; // 通过接口类成员属性可以获取经过PhalApi框架过滤、检测和格式化后的客户端参数
        $pass = $this->password;

        return array('username' => $name, 'password' => $pass);
    }                               
}
```

当请求此接口服务，并类似这样带上username和password参数时：  
```
/?s=User.Login&username=dogstar&password=123456
```
就可以得到这样的返回结果。  
```
{"ret":200,"data":{"username":"dogstar","password":"123456"},"msg":""}
```


## 接口返回

回顾一下，在PhalApi中，接口返回的结果的结构为：  
```html
{
    "ret": 200, // 状态码
    "data": {
        // 业务数据，推荐统一返回对象结构，保持类型一致性，且便于扩展和升级
    },
    "msg": "" // 错误提示信息
}
```

### 正常情况下的返回

正常情况下，在Api层返回的数据结果，会在返回结果的data字段中体现。例如：  
```php
class Hello extends Api {

    public function world() {
        return array('title' => 'Hello World!');
    }
}
```

对应：  
```
{
    "ret": 200,
    "data": {
        "title": "Hello World!"
    },
    "msg": ""
}
```
成功返回时，状态码ret为200，并且错误信息msg为空。  

### 失败情况下的返回

对于异常情况，包括系统错误或者应用层的错误，可以通过抛出[PhalApi\Exception](https://github.com/phalapi/kernal/blob/master/src/Exception.php)系列的异常，中断请求并返回相关的错误信息。例如：  

```php
class Hello extends Api {

    public function fail() {
        throw new BadRequestException('签名失败', 1);
    }
}
```

会得到以下结果输出：  
```
{
    "ret": 401,
    "data": {},
    "msg": "Bad Request: 签名失败"
}
```

### 返回其他状态码

若项目需要自定义ret状态，丰富其语义，即需要返回200~299、400~499、500~599以外的ret状态码，可以通过自定义业务异常类来实现，或者手动指定ret状态码。

#### 方式一：通过异常返回ret状态码

此时，自定义异常类应继承于```PhalApi\Exception```框架异常类。  

首先，在Common目录下添加新文件，实现新业务异常类。  
```php
<?php
// 文件 ./src/app/Common/AppException.php

namespace App\Common;
use PhalApi\Exception;

class AppException extends Exception {
}
```

然后，在需要的地方抛出```App\Common\AppException```异常。此异常作为信号量，会通过PhalApi框架中止请求，并把相应的状态码和错误提示返回给客户端。  

例如：  
```php
<?php
namespace App\Api;
use PhalApi\Api;
use App\Common\AppException;

class User extends Api {
    public function login() {
        throw new AppException('提示消息', 1000);

        // 正常情况下，data部分的返回数据
        return array('user_id' => 8);
    }
} 
```
接口返回结果是：
```
{
    "ret": 1000,
    "data": {},
    "msg": "提示消息"
}
```
注意，此时data为空对象。  

#### 方式二：手动指定ret状态码

如果不希望抛出异常，或者想手动指定ret和msg，或者希望ret为非200时依然能输出data业务数据，那么可以通过手动设置```\PhalApi\DI()->response```服务的ret和msg。例如：  
```php
<?php
namespace App\Api;
use PhalApi\Api;

class User extends Api {

    public function login() {
        // 手动设置ret为1000
        // ret=200时表示正常返回，ret=4xx表示额端非法请求，ret=500表示服务器内部错误，手动设置时应设置成其他整数范围，避免语义冲突
        \PhalApi\DI()->response->setRet(1000);

        // 手动设置提示消息
        \PhalApi\DI()->response->setMsg('手动设置提示消息');

        // 正常情况下，data部分的返回数据
        return array('user_id' => 8);
    }
} 
```

通过：  

 + ```\PhalApi\DI()->response->setRet(1000);```：手动设置ret状态码
 + ```\PhalApi\DI()->response->setMsg('手动设置提示消息');```：手动设置msg提示消息  

在接口正常请求后，会返回以下接口结果：  
```
{
    "ret": 1000,
    "data": {
        "user_id": 8
    },
    "msg": "手动设置提示消息"
}
```

需要注意的是，如果请求被PhalApi拦截，或者因抛出其他异常导致未能正常返回接口结果，以上手动设置ret和msg将不起作用。  

## 扩展：返回JSONP、XML等其他格式

### JSONP返回格式

如果需要支持JSONP返回格式，可以将 ```./config/di.php``` 中的以下代码注释去掉：  

```
// 支持JsonP的返回
if (!empty($_GET['callback'])) {
    $di->response = new \PhalApi\Response\JsonpResponse($_GET['callback']);
}
```

然后在请求时，传入回调函数的名称callback，即可返回JSONP格式。例如请求：
```
http://dev.phalapi.net/?s=Hello.World&callback=test
```

返回：  
```
test({"ret":200,"data":{"title":"Hello World!"},"msg":""})
```

### XML返回格式

如果需要返回XML格式，需要将```\PhalApi\DI()->response```切换到XML响应类，如：  

```
$di->response = new \PhalApi\Response\XmlResponse();
```

然后，可看到请求的接口返回类似如下：  
```
<?xml version="1.0" encoding="utf-8"?><xml><ret><![CDATA[200]]></ret><data><title><![CDATA[Hello World!]]></title></data><msg><![CDATA[]]></msg></xml>
```

### 其他返回格式  

常用的返回格式有如上的JSON、JSONP、XML返回格式。如果需要返回其他的格式，你可以：

 + 1、实现\PhalApi\Response抽象中的formatResult($result)格式化返回结果  
 + 2、重新注册\PhalApi\DI()->response服务  

如果希望能由客户端指定返回格式，可通过参数来动态切换。 

## 扩展：修改默认返回的ret/data/msg结构

对于默认返回的字段结构，源代码实现在[PhalApi\Response::getResult()](https://github.com/phalapi/kernal/blob/master/src/Response.php#L165)方法。相关代码片段如下：  

```php
abstract class Response {
    public function getResult() {
        $rs = array(
            'ret'   => $this->ret,
            'data'  => is_array($this->data) && empty($this->data) ? (object)$this->data : $this->data, // # 67 优化
            'msg'   => $this->msg,
        );
        if (!empty($this->debug)) {
            $rs['debug'] = $this->debug;
        }
        return $rs;
    }
}    
```

如果需要修改默认的ret/data/msg，可以重载此方法，然后进行修改。

> 温馨提示：PhalApi 2.11.0 及以上版本，可直接通过修改./config/sys.php配置的response.structure_map配置项来实现。  

值得注意的是，你可以基于现有的具体响应类进行继承重载。例如针对JSON的返回格式，先添加一个自己的扩展子类。例如只返回data部分：  

```php
<?php
// 新建 ./src/app/Common/MyResponse.php 文件

namespace App\Common;

use PhalApi\Response\JsonResponse ;

class MyResponse extends JsonResponse {
    public function getResult() {
        $rs = parent::getResult();

        $newRs = array(
                'error_status' => $rs['ret'],
                'error_message' => $rs['msg'],
                'result' => $rs['data'],
                );
        return $newRs;
    }
}
```

接着在 ./config/di.php 文件中重新注册```$di->response```服务为此新的响应类实例。例如：  
```php
$di->response = new \App\Common\MyResponse();
```

完成子类重载，以及response服务重新注册后，再次访问接口，就会看到返回的结果去掉了ret和msg部分。  

例如，对于Hello World接口服务，原来返回是：    
```
{
    "ret": 200,
    "data": {
        "title": "Hello PhalApi",
        "version": "2.10.1",
        "time": 1581425013
    },
    "msg": ""
}
```

现在，返回是（error_status/error_message/result结构）：  
```
{
    "error_status": 200,
    "error_message": "",
    "result": {
        "title": "Hello PhalApi",
        "version": "2.10.1",
        "time": 1581426712
    }
}
```
