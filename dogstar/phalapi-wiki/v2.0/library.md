# PhalApi框架扩展类库

## 扩展类库简介

### 致力于与开源项目一起提供企业级的解决方案！

此部分类库为PhalApi框架下可重用的扩展类库，各个扩展相自独立，可以根据需要自动安装使用。

此扩展类库可以是基于已有的第三方开源类库的二次开发和集成，也可以是自主研发的组件、工具、模块。通过使用可重用的扩展类库，可大大减少开发成本，并且慢慢地会发现，原来编程本来就是一件如此简单的事情，就像搭积木一样。

正如我们一直提倡的：**接口，从简单开始！**  


## 扩展类库列表  

扩展类库composer名称|扩展类库名称|简要说明
---|---|---
[phalapi/apk](https://github.com/wenzhenxi/phalapi2-apk)|APK文件解包处理|对APK进行解包，支持绝大部分APK文件处理。
[phalapi/auth](https://github.com/twodayw/auth.git)|Auth权限扩展|实现了基于用户与组的权限认证功能，与RBAC权限认证类似，主要用于对服务级别的功能进行权限控制。 ```by twodayw```  
[phalapi/cli](https://github.com/phalapi/cli)|CLI扩展类库|可用于开发命令行应用，基于GetOpt，主要作用是将命令参数进行解析和处理。 
Cluster|基于PhalApi的DB集群拓展|为了解决大量数据写入分析的问题，支持大量select、和大量insert。
CryptTraffic|移动设备通信加密|用于移动设备通信加密。 
Excel|PhalApi-Excel|读取Excel。  
Facepp|face++接口|face++接口。
[phalapi/fast-route](https://github.com/phalapi/fast-route)|FastRoute快速路由|基于FastRoute实现，通过配置实现自定义路由配置，从而轻松映射service接口服务。
[phalapi-image](https://github.com/gongshunkai/phalapi-image)|PhalApi-Image图像处理|按照尺寸压缩上传图片，参考自ThinkPhP图形处理。```by 吞吞小猴```  
KafKa|简单舒适的PHP-KafKa拓展|基于rdKafKa封装的一个简单舒适KafKa拓展。
Log4php|基于log4php的日志扩展|兼容PhalApi日志的接口操作，同时基于log4php完成更多出色的日志工作。
Medoo|Medoo数据库驱动|Medoo数据库驱动。  
[phalapi-aliyun-oss](https://github.com/vivlong/phalapi-aliyun-oss)|PhalApi-OSS阿里云OSS包|对阿里云的OSS文件服务器的封装。```by vivlong```
PHPExcel|PhalApi-PHPExcel扩展|提供了更为强大的Excel处理功能。
[phalapi/PHPMailer](https://github.com/phalapi/PHPMailer)|基于PHPMailer的邮件发送|用于发送邮件。
[phalapi-phprpc](https://github.com/phalapi/phprpc)|代理模式下phprpc协议的轻松支持|可用于phprpc协议的调用，服务端只需要简单添加入口即可完美切换。
[phalapi-pay](https://github.com/phalapi/pay)|基于PhalApi的第三方支付扩展|支持微信支付和支付宝支付。
Payment|微信支付及支付宝支付扩展|支持微信支付和支付宝支付。
[phalapi/qiniu](https://github.com/phalapi/qiniu)|七牛云存储接口调用|可用于将图片上传到七牛云存储，或者七牛SDK包提供的其他功能。
RabbitMQ|PhalApi-RabbitMQ队列拓展|基于队列标杆中的RabbitMQ的队列扩展。
[phalapi/redis](https://github.com/wenzhenxi/phalapi2-redis)|基于PhalApi的Redis拓展|提供更丰富的Redis操作，并且进行了分库处理可以自由搭配。```by 喵了个咪```
[phalapi-sms](https://github.com/gongshunkai/phalapi-sms)|PhalApi-SMS容联云短信服务器扩展|基于容联云通讯，发送短信。
[ctbsea/phalapi-smarty](https://github.com/ctbsea/phalapi-smarty)|基于PhalApi的Smarty扩展|基于老牌的PHP模版引擎Smarty，提供视图渲染功能。
[chenall/phalapi-soap](https://github.com/chenall/phalapi-soap)|SOAP扩展|使用PHP官方提供的SOAP协议，用于搭建Web Services。```by chenall```
Swoole|Swoole扩展|基于swoole，支持的长链接和异步任务实现。
[phalapi/task](https://github.com/phalapi/task)|计划任务扩展|用于后台计划任务的调度。
ThirdLogin|第三方登录扩展|第三方登录。
Translate|PhalApi-Translate百度翻译扩展|基于百度翻译的翻译。
UCloud|图片上传扩展|用于图片文件上传。
[phalapi/user](https://packagist.org/packages/phalapi/user)|User用户扩展|提供用户、会话和集成第三方登录。```by detachment```
[steveak/view](https://github.com/xubing6243/phalapi-view)|View视图扩展|提供视图渲染功能。```by steve```
Wechat|微信开发扩展|可用于微信的服务号、订阅号、设备号等功能开发。
Xhprof|性能分析工具PhalApi-Xhprof|对Facebook开源的轻量级PHP性能分析工具进行了封装拓展。
YoukuClient|优酷开放平台接口扩展|用于调用优酷开放平台的接口。
Zip|PhalApi-Zip压缩文件处理|用于处理文件压缩。
[phalapi/qrcode](https://github.com/phalapi/qrcode)|PhalApi 二维码扩展|二维码扩展，基于PHP QRCode实现。可用于生成二维码图片。  
[phalapi/pinyin](https://github.com/phalapi/pinyin)|PhalApi 2.x 拼音扩展|PhalApi 2.x 拼音扩展，基于overtrue/pinyin实现。
[phalapi-gtcode](https://github.com/gongshunkai/phalapi-gtcode)|极验验证码扩展|极验验证码扩展，```by 吞吞小猴```
[phalapi/jwt ](https://github.com/twodayw/phalapi2-jwt)|基于PhalApi2的JWT拓展|JSON Web Token（JWT）是一个非常轻巧的规范。这个规范允许我们使用JWT在用户和服务器之间传递安全可靠的信息。```by twodayw```  
[chenall/phalapi-weixin](https://github.com/chenall/phalapi-weixin)|微信扩展|微信公众号、企业号等开发扩展, 使用Eastwechat。 ```by chenall```  
[phalapi/wechatmini](https://github.com/JamesLiuquan/wechatmini)|微信小程序扩展|PhalApi 2.x 微信小程序扩展 ```by JamesLiuquan```  
[chenall/phalapi](https://github.com/chenall/phalapi)|请求参数规则扩展| ```by chenall```
[phalapi/cors](https://github.com/gongshunkai/phalapi-cors)|CORS跨域扩展|```by gongshunkai(吞吞小猴)```
[phalapi/session](https://github.com/Zhangzijing/session)|Session 操作工具|phalapi session 会话封装 ```by Zhangzijing```
[phalapi/mongo](https://github.com/logmecn/phalapi-mongo)|基于 PhalApi2 框架的 MongoDB 扩展|使用 MongoDB 存储数据时，用到此扩展。```by logmecn```
[phalapi/phalapimp](https://gitee.com/kaihangchen_admin/phalapimp)|微信扩展|微信公众号、企业号等开发扩展, 基于Eastwechat。 ```by 梦小星```
[phalapi/xunhupay](https://github.com/phalapi/xunhupay)|虎皮椒支付扩展|PhalApi 2.x 虎皮椒支付扩展
[symochan/phalapi-usercheck](https://github.com/hs9206/phalapi-usercheck)|第三方用户登陆检测 UserCheck|PhalApi 2.x扩展类库，第三方用户登陆检测 UserCheck
[phalapi/dingbot](https://gitee.com/kaihangchen_admin/DingBot)|PhalApi的钉钉群webhook机器人扩展|```by NullUserException```


> 温馨提示：未有composer链接的，表示尚未从1.x迁移到2.x版本，可在原来的[Phalapi-Library扩展类库](https://github.com/phalapi/phalapi-library)项目中查阅。


## 扩展类库的使用

对于某个扩展类库，当需要使用时，可以按“安装、配置、使用”三步曲进行。  

#### 安装

扩展类库的安装很简单，在PhalApi 2.x版本下，直接通过在composer.json文件中配置需要依赖的扩展类库即可。  

例如，项目本身自带的Task扩展类库：  
```
{
    "require": {
        "phalapi/task": "2.0.*"
    }
}
```

配置好后，执行```composer update```更新操作即可。  

至此，便完成了扩展类库的安装，相当简单。  

#### 配置注册

根据不同的扩展类库，其配置和注册的情况不同，有些不需要配置也不需要注册，有些需要配置、注册中的一种，有些可能配置、注册都需要。  

##### 何为配置？

这里说的配置是指在项目配置文件./config/app.php内添加对应扩展类库的配置，配置选项的路径通常为：```app.扩展类库名称```。此外，有的扩展类库可能还需要配置数据库配置文件./config/dbs.php。

##### 何为注册？

而注册则是指将对应的扩展类库注册到DI容器```\PhalApi\DI()```中，需要在./config/di.php文件中配置。注册的服务名称通常为扩展类库的小写名称。  

注册好后，便可以在项目需要的位置进行调用了。  

#### 使用

不同的扩展类库，其提供的功能不同，所以具体的使用也不尽相同。当使用到某个扩展类库时，可以参考对应的文档说明。有的扩展可能需要调用其内部接口才能实现对应的功能，有些扩展可能提供了直接可用的接口服务。  


## 如何开发扩展类库？

通过编写开发PhalApi 2.x 扩展类库，你可以在项目中快速集成各种扩展类库，同时可以开源给更多开发者和项目使用，从而提升自己的技术影响力。  

### 建议
为了统一扩展类库的风格、便于用户更容易使用，这里建议：

+ 代码：遵循composer和psr-4的风格，并尽量Lite.php为入口类，一个扩展，一个Git项目，源代码可放置在自己的Git仓库；
+ composer：建议统一注册在[phalapi](https://packagist.org/packages/phalapi/phalapi)下，可联系dogstar；
+ 配置：统一放置在```\PhalApi\DI()->config->get('app.扩展包名')```中，避免配置冲突；
+ 文档：统一提供README.md文件，对扩展类库的功能、安装和配置、使用示例以及运行效果进行说明；

### 扩展类库开发详细教程

第一步，先在Github或者码云或者其他Git代码托管平台创建一个新仓库。仓库名称自已定义，和平时Git项目一样。  
第二步，在根目录添加composer.json（或者使用命令：composer init），参考：  
```
  
{
    "name": "phalapi/demo",
    "description": "PhalApi 2.x 扩展示例",
    "type": "library",
    "keywords": [
        "phalapi-demo"
    ],
    "license": "GPL-3.0+",
    "minimum-stability": "dev",
    "homepage": "https://www.phalapi.net/",
    "authors": [
        {
            "name": "dogstar huang",
            "email": "chanzonghuang@gmail.com",
            "homepage": "http://my.oschina.net/dogstar",
            "role": "Developer"
        }
    ],
    "require": {
    },
    "autoload": {
        "psr-4": {
            "PhalApi\\demo\\": "src"
        }
    } 
}
```
第三步，在根目录下添加src目录，放置扩展的源代码。如果需要再添加config、data、public等辅助目录。  
第四步，进行扩展类库的代码编写和功能开发。  
第五步，扩展类型开发完成并测试通过后，提交代码到Git平台。  
第六步，发布你的composer包。  
进入[https://packagist.org/](https://packagist.org/)创建一个新的composer包，免费注册后点击右上角的Sumbit按钮。  
![](http://cdn7.okayapi.com/yesyesapi_20200114233312_1db28ff93df68a2fa5dfc940ac646d03.png)  
输入你上面的Git仓库地址然后进行Chekc和提交。  
![](http://cdn7.okayapi.com/yesyesapi_20200114233348_e7215317aa9d5c137d5b9972cfcb962f.png)
最后，成功提交后就可以看到类似PhalApi核心类库这样的效果：  
![](http://cdn7.okayapi.com/yesyesapi_20200114233519_315b6232f76c9b0d5d49a5234e0b1159.png)

接下来，就可以提供给全世界的开发者使用啦！

### 如何在扩展类库中直接提供接口？

可以在扩展类库里面的src目录，分别添加Api、Domain、Model和Common目录，和普通的接口项目开发一样。然后放置需要在扩展类库里直接对外提供的API接口。  

以[PhalApi 2.x 虎皮椒支付扩展](https://github.com/phalapi/xunhupay)为例，
它注册的全名空间是：  
```
"autoload": {
    "psr-4": {
        "PhalApi\\Xunhupay\\": "src"
    }
} 
```
它的目录结构是：  
```
└── src
├── Api
│   └── Xunhupay.php
├── Model
│   └── XunhupayOrder.php
```
重点文件是./src/Api/Xunhupay.php，里面有两个接口，分别是支付和查询接口。关键代码片段是：  
```php
<?php
namespace PhalApi\Xunhupay\Api;
class Xunhupay extends \PhalApi\Api {
    /**
     * 发起支付接口
     * @desc 发起支付接口，实现微信、支付宝支付的接口
     */
    public function paymentDo() {
    }

    /**
     * 订单查询接口
     * @desc 订单查询接口
     */
    public function orderQuery() {
    }
}
```

当开发者在使用此扩展类库时，安装好composer包后，还需要在composer.json目录里面手动添加psr-4中配置，以便在线接口文档可以加载扩展里面的接口。例如：  
```
{
    "autoload": {
        "psr-4": {
            "PhalApi\\Xunhupay\\":"vendor/phalapi/xunhupay/src",
            "App\\": "src/app"
        }
    }
}
```
此时，左边的key是扩展类库的命名空间，右边的值则对应vendor目录下你扩展类库的src路径。  

最终，扩展类库的使用方通过在线接口文档列表可以看到扩展类库里面的接口。  
![](http://cdn7.okayapi.com/yesyesapi_20200114234259_15d04cb89d0a3739846be7b7c45e6da9.png)  

点击后可查看相应的接口详情文档：  
![](http://cdn7.okayapi.com/yesyesapi_20200114234348_5cb45a1cee93ab1d0466e0578f28d8a6.png)

进行在线接口测试的效果：  
![](http://cdn7.okayapi.com/yesyesapi_20200114234440_10e7fc29fa2800447d4e1f584d7acade.png)



### 在composer下开发扩展类库的建议

 + 约定上依赖phalapi/kernal， 但扩展类库内不要配置依赖，以免框架升级导致扩展不兼容  
 + 为简化起见，扩展类库默认统一使用master分支作为安装版本，免去多版本管理 

### 从微架构到扩展类库的演进

在应用项目的实际开发，我们也可以有意识地将一些通用的工具和操作与业务分离，以便可以在项目内更好地重用。当抽离成工具或者通用类后，则可以进一步推广到公司内其他项目，即组件复用。如果觉得可以，则发扬开源精神，分享给社区。这也是符合从微架构到应用构架、系统架构、乃至企业架构的演进之路。  

 + 
