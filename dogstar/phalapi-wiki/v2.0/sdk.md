# PhalApi 2.x 的SDK包

## SDK包列表

已经支持的SDK有：  

 + [Java版SDK](https://github.com/phalapi/phalapi/tree/master-2x/sdk/JAVA/)
 + [Object-C版SDK](https://github.com/phalapi/phalapi/tree/master-2x/sdk/Objective-C/PhalApiClient)
 + [Object-C版SDK，遵循AFNetworking](https://github.com/phalapi/phalapi/tree/master-2x/sdk/Objective-C/XSHttpTool)
 + [Javascript版SDK](https://github.com/phalapi/phalapi/tree/master-2x/sdk/JS)
 + [Golang版SDK](https://github.com/phalapi/phalapi/tree/master-2x/sdk/Go)
 + [PHP版SDK](https://github.com/phalapi/phalapi/tree/master-2x/sdk/PHP/)
 + [Python版SDK](https://github.com/phalapi/phalapi/tree/master-2x/sdk/Python/)
 + [React-Native版SDK](https://github.com/phalapi/phalapi/tree/master-2x/sdk/React-Native)
 + [Ruby版SDK](https://github.com/phalapi/phalapi/tree/master-2x/sdk/Ruby)


## 一句话描述

为了给客户端统一接口请求调用的规范性、流畅性和简单易懂，我们特别为此使用了内部领域特定语言： **接口查询语言** （Api Structured Query Language） 。  
   
### 外部DSL

从外部DSL的角度来看待接口查询的操作
```
create

withHost host
withFilter filter
withParser parser

reset   #特别注意：重复查询时须重置请求状态

withService service
withParams paramName1 paramValue1
withParams paramName2 paramValue2
withParams ... ...
withTimeout timeout

request

```
  
根据此设计理念，各客户端语言都可以实现此接口请求的操作。 

## 接口查询语言设计理念与示例

### 文法：create -> with -> request
所用到的查询文法如下（通常从上往下依次操作，顺序不强制）：  
  
操作|参数|是否必须|是否可重复调用|作用说明
---|---|---|---|---
create|无|必须|可以，重复调用时新建一个实例，非单例模式|需要先调用此操作创建一个接口实例
withHost|接口域名|必须|可以，重复时会覆盖|设置接口域名，如：http://demo.phalapi.net/
withFilter|过滤器|可选|可以，重复时会覆盖|设置过滤器，与服务器的```PhalApi\DI()->filter```对应，需要实现PhalApiClientFilter接口
withParser|解析器|可选|可以，重复时会覆盖|设置结果解析器，仅当不是JSON返回格式时才需要设置，需要实现PhalApiClientParser接口
reset|无|通常必须|可以|重复查询时须重置请求状态，包括接口服务名称、接口参数和超时时间
withService|接口服务名称|通常必选|可以，重复时会覆盖|设置将在调用的接口服务名称，如：Site.Index
withParams|接口参数名、值|可选|可以，累加参数|设置接口参数，此方法是唯一一个可以多次调用并累加参数的操作
withTimeout|超时时间|可选|可以，重复时会覆盖|设置超时时间，单位毫秒，默认3秒
request|无|必选|可以，重复发起接口请求|最后执行此操作，发起接口请求


### JAVA示例

以JAVA版本为例，演示如何调用：  
最简单的调用，也就是默认接口的调用：  

```java
PhalApiClientResponse response = PhalApiClient.create()
       .withHost("http://demo.phalapi.net/")  //接口域名
       .request();                            //发起请求
```
  
通常的调用，即有设置接口服务名称、接口参数和超时：
```java
PhalApiClientResponse response = PhalApiClient.create()
       .withHost("http://demo.phalapi.net/")
       .withService("Site.Index")          //接口服务
       .withParams("username", "dogstar")     //接口参数
       .withTimeout(3000)                     //接口超时
       .request();
```
  
高级复杂调用，即设置了过滤器、解析器的操作：  
```java
PhalApiClientResponse response = PhalApiClient.create()
       .withHost("http://demo.phalapi.net/")
       .withService("Site.Index")
       .withParser(new PhalApiClientParserJson()) //设置JSON解析，默认已经是此解析，这里仅作演示
       .withParams("username", "dogstar")
       .withTimeout(3000)
       .request();
```

## 更好的建议

### 不支持面向对象的实现方式

此接口查询的用法是属于基础的用法，其实现与宿主语言有强依赖关系，在不支持面向对象语言中，如javascript，可以使用函数序列的方式，如：  
```
create();
withHost('http://demo.phalapi.net/');
withService('Site.Index');
withParams('username', 'dogstar');
withTimeout(3000);
var rs = request();
```

### 封装自己的接口实例

通常，在一个项目里面我们只需要一个接口实例即可，但此语言没默认使用单例模式，是为了大家更好的自由度。  
基于此，大家在项目开发时，可以再进行封装：提供一个全局的接口查询单例，并组装基本的接口公共查询属性。  
  
即分两步：初始化接口实例，以及接口具体的查询操作。  
  
如第一步先初始化：
```java
PhalApiClient client = PhalApiClient.create()
     .withHost("http://demo.phalapi.net/")
     .withParser(new PhalApiClientParserJson());
```
  
第二步进行具体的接口请求：  
```java
PhalApiClientResponse response = client.reset()  #重复查询时须重置
     .withService("Site.Index")
     .withParams("username", "dogstar")
     .withTimeout(3000)
     .request();
```
  
这样，在其他业务场景下就不需要再重复设置这些共同的属性（如过滤器、解析器）或者共同的接口参数。

### 超时重试
当接口请求超时时，统一返回 ret = 408，表示接口请求超时。此时可进行接口重试。  
  
如：  
```java
PhalApiClient client = PhalApiClient.create()
     .withHost("http://demo.phalapi.net/")

PhalApiClientResponse response = client.request();

if (response.getRet() == 408) {
     response = client.request(); //请求重试
}
```
***
