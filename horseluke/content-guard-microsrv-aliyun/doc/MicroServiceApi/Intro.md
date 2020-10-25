# 基于阿里云安全接口的内容安全微服务API接口说明和列表

## 介绍

本微服务主要集成阿里云安全云盾魔方的接口，然后以http api的形式，为内网提供持续的内容检查和发现机制。

## API认证方式

使用HTTP普通鉴权(Basic Authentication)方式。

该方式通过HTTP头传递用户身份的授权方式。故该微服务只适合内网使用（详细见faq[“为什么定位为内网服务？不能外网使用？”](http://git.oschina.net/horseluke/content-guard-microsrv-aliyun/blob/master/doc/Faq.md)）。

点此查看[HTTP普通鉴权(Basic Authentication)方式](http://git.oschina.net/horseluke/content-guard-microsrv-aliyun/blob/master/doc/MicroServiceApi/BasicAuthentication.md)。

## 公共参数

公共参数应放置于url query string中。系统将以GET方式获取。

注意：无论变换成什么API认证方式，这些公共参数都必须提供。详细见faq[“为什么请求公共参数要放置于url中？”](http://git.oschina.net/horseluke/content-guard-microsrv-aliyun/blob/master/doc/Faq.md)


|   参数名称       |   类型              |  必须？    |    说明                           | 示例                    |
|-------------|------------|---------|------------------|------------|
| r           | string     |      Y  |    API名称                   |  index/index/ping   |
| m_appid           | int     |      Y  |    分配的微服务appkey，当前为数字   |  1   |
| m_ver           | string     |      Y  |    API版本号，当前为1.0   |  1.0   |
| m_ip           | string     |      Y  |    请求用户的客户端ip（不是使用api的服务器ip），IPV4格式   |  127.0.0.1   |

## 返回结构

返回结构为一个json。

请求正常时，该json的code字段为0（int类型），rst字段为请求结果的具体内容。示例：

```
  {"rst": "__data__","code":0}
```

若出错，该json的code不为0，并且返回err字段（字符串类型），和errdetail字段（混合类型）。rst字段一般为false，但特殊情况下会返回部分数据（不提倡）。

```
  {"rst": false,"code":1,"err":"111111","errdetail":null}
```

使用者只需判断是否存在code字段，并且code是否全等于0，即可判断该次请求是否成功。php代码：

```
if(isset($result['code']) && $result['code'] === 0){
    var_export($result['rst']);
}else{
    exit("请求失败！");
}
```



  
## 接口列表

（源代码实现，请见目录```/protected/App/Class/ControllerMicroServiceApi```）

（详细接口请求参数和返回内容，待补充）

* content接口
  * content/validator/checkSpam
  
* index接口
  * index/index/ping
  
* ip接口
  * ip/query/attackHistory
  * ip/query/geoStat
