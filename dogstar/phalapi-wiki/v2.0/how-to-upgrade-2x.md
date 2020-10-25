# PhalApi 2.x 升级指南 

PhalApi自2015年初正式开源以来，版本主要经历了 v1.0.0 ~ v1.4.1，其间，我们一直在致力于“接口，从简单开始！”的同时，也致力框架的兼容性升级。但考虑到2.x版本是全新的实现机制和方式，经过综合和慎重考虑，2.x版本将不再支持向前兼容，即1.x版本不能完美升级到2.x版本。对此带来的不便，我深表歉意。  

为方便1.x版本的开发者更容易从概念上切换到2.x版本，以下是快速升级指南。  

## 从石器农耕到现代自动化

自2017年x月x日发布 v2.0.0 版本开始，PhalApi社区主要存在两大系列版本，分别是：  

 + **PhalApi v1.x 版本**  slogan：接口，从简单开始！
 + **PhalApi v2.x 版本**  slogan：助你创造价值！

这两系列版本，最大的差异在于，PhalApi v2.x 版本是：  

 + 使用了composer
 + 引入了命名空间
 + 遵循psr-4规范

相比之下，PhalApi v1.x 版本则是：  

 + 未使用composer，自主构建自动加载器PhalApi_Loader
 + 完全不使用命名空间
 + 遵循psr-0规范以及PEAR命名规范

使用composer的方式，更有利于可重用组件的管理，并且向国际化、向主流靠拢，同时也要求我们需要对PhalApi进行重新划分与调整。而引入命名空间，则更是要求核心框架代码、扩展类库乃至应用项目开发，都需要重新调整。  

这是历史发展的必然趋势，因为composer和命名空间将会被越来越多的开发人员所熟悉。如果在1.x版本，我们是石器农耕时代，那么2.x版本，我们迎来的将是现代自动化时代。  

> 温馨提示：关于composer，更多可访问：[Composer 中文网 / Packagist 中国全量镜像](http://www.phpcomposer.com/)。


## composer的使用方式

在1.x版本中，PhalApi更多是以自主研发的自动加载方式为主，这种方式原始、简单，并且粗糙。在2.x版本中，PhalApi使用的是composer管理方式，这要求对已有的代码仓库进行重新划分，并分为：项目、扩展类库和核心框架这三部分。  

 + 项目：可用于实际项目开发，提供给开发人员使用。
 + 扩展类库：由广大开发人员共同维护、分享。
 + 核心框架：由PhalApi团队核心人员长期维护，并接受贡献者的merge request。 

### 如何创建新项目？

如果需要使用PhalApi 2.x 版本开发新项目，可以直接使用[phalapi/phalapi](https://github.com/phalapi/phalapi)。  

在composer方式下，当需要自动加载类和函数时，可使用autoload配置，参考：  
```
{
    "autoload": {
        "files": [
            "src/app/functions.php"
            ],
        "psr-4": {
            "App\\": "src/app"
        }
    }
}
```

### 如何使用扩展类库?

当需要使用扩展类库时，你只需要简单的告诉 Composer 需要依赖哪些包。在composer.json中添加相应的包名称和版本信息，然后更新即可。以添加Task计划任务扩展为例，首先修改composer.json并添加：  
```
{
    "require": {
        "phalapi/task": "1.0.*"
    }
}
```
然后，进行composer更新：  
```bash
$ composer update
```

如果扩展类库还提供了直接可用的接口服务，这里则还需要配置psr-4，以便让composer能正常自动加载对应的类文件，并且显示在在线接口文档。

### 如何升级框架？

借助composer，可轻松更新框架，同时也可以指定需要依赖的具体版本。例如，若希望从2.0.0升级到2.0.1版本，可以： 
```
{
    "require": {
        "phalapi/kernal": "2.0.1"
    }
}
```
然后，进行composer更新即可。


## 命名空间下的差异

由于PhalApi 1.x 版本不使用命名空间，而PhalApi 2.x 版本使用了命名空间，因此会有所差异。主要有：  

### 接口服务请求时的差异

1.x 版本的默认接口服务是```Default.Index```，对应文件```./Demo/Api/Default.php```；2.x 版本的默认接口服务是```App.Site.Index```，对应文件```./src/app/Api/Site.php```。  

1.x 版本的接口服务的请求格式是：Class.Action；2.x 版本的接口服务的请求格式是：Namespace.Class.Action，比旧版本多了命名空间前缀，并且为了兼容原来的格式，缺省的命名空间是App。当命名空间和类名空间存在多组时，将会使用下划线分割。以下是2.x版本的一些示例：  

2.x 请求的service参数|对应的文件|执行的类方法
---|---|---
无|./src/app/Api/Site.php|App/Api/Site::Index()
?s=Site.Index|./src/app/Api/Site.php|App/Api/Site::index()
?s=Weibo.Login|./src/Api/Weibo.php|App/Api/Weibo::login()
?s=User.Weibo.Login|./src/user/Api/Weibo.php|User/Api/Weibo::login()
?s=App_User.Third_Weibo.Login|./src/app/user/Api/Third/Weibo.php|App/User/Api/Third/Weibo::login()

### Model层自动匹配的表名差异

在1.x 版本中，默认表名的自动匹配规则是：取Model_后面部分的字符全部转小写。例如：  

```php
<?php
class Model_User extends PhalApi_Model_NotORM {
}
```
类Model_User自动匹配的表名为```user```。  

在2.x 版本中，规则类似，但由于多了命名空间，默认表名的自动匹配规则是：取```\Model\```后面部分的字符全部转小写，将用下划线分割。例如：  
```php
<?php
namespace App\Model\User;
use PhalApi\Model\NotORMModel as Model;

class Friends extends Model {
}
```
则类App\Model\User\Friends自动匹配的表名为```user_friends```。以下是2.x版本的一些示例：  

2.x 的Model类名|对应的文件|自动匹配的表名
---|---|---
App\Model\User|./src/app/Model/User.php|user
App\ModelUser\Friends|./src/app/Model/User/Friends.php|user_friends
App\User\Model\Friends|./src/app/user/Model/Friends.php|friends
App\User\Model\User\Friends|./src/app/user/Model/User/Friends.php|user_friends

和1.x 版本相同的是，自动匹配的表名后面会自动加上表前缀。 

当自动匹配的表名不能满足实际开发需求时，1.x 和 2.x 版本均支持自定义表名。  

## 可访问的入口差异

在PhalApi 1.x 版本中，对外可访问的入口主要有：  

 + 不同项目的访问入口，如：/Public/demo/index.php
 + 在线接口列表文档，如：/Public/demo/listAllApis.php
 + 在线接口详情文档，如：/Public/demo/checkApiParams.php

如何访问在线接口文档？

鉴于PhalApi 1.x版本中的访问入口过于分散，且在线接口文档链接过于冗长，在PhalApi 2.x 版本中，我们同时对此进行了优化。即精简为：  

 + 访问入口默认只有一个，即：/public/index.php
 + 在线接口列表与接口详情文档，二合为一，链接改为：/public/docs.php

## 项目目录结构的差异

在PhalApi 1.x中，项目目录结构为：  
```bash
.
│
├── PhalApi         //PhalApi框架，后期可以整包升级
├── Library         //PhalApi扩展类库，可根据需要自由添加扩展
├── SDK             //PhalApi提供的SDK包，客户可根据需要选用
│
│
├── Public          //对外访问目录，建议隐藏PHP实现
│   └── demo        //Demo服务访问入口
│
│
├── Config          //项目接口公共配置，主要有：app.php, sys.php, dbs.php
├── Data            //项目接口公共数据
├── Language        //项目接口公共翻译
├── Runtime         //项目接口运行文件目录，用于存放日记，可软链到别的区
│
│
└── Demo            //应用接口服务，名称自取，可多组
    ├── Api             //接口响应层
    ├── Domain          //接口领域层
    ├── Model           //接口持久层
    └── Tests           //接口单元测试
```

在PhalApi 2.x中，项目目录结构为：  
```bash
.
│ 
├── config                  // 项目接口公共配置
│   ├── app.php             // 项目配置
│   ├── dbs.php             // 数据库配置
│   ├── di.php              // DI依赖注入配置
│   └── sys.php             // 系统环境配置
│ 
├── public                  // 对外访问目录，推荐将web根路径设定在此目录
│   ├── docs.php            // 自动生成的在线接口文档
│   ├── examples            // 示例
│   ├── index.php           // 接口服务统一访问入口
│   └── init.php            // 统一初始化文件
├── runtime                 // 项目运行时产生的文件目录
│ 
├── src                     // 项目PHP源代码
│   └── app                 // 默认使用此App命名空间，可创建多上命名空间
│       ├── Api             // 接口响应控制层
│       ├── Domain          // 接口领域业务层
│       ├── functions.php   // 面向过程式的函数
│       └── Model           // 接口数据模型层
├── tests                   // 单元测试
│ 
├── bin                     // 脚本命令
├── data                    // 用于存放SQL建表基本语句
├── language                // 语言翻译包
├── sdk                     // 客户端SDK开发包，支持9+种语言
│ 
├── composer.json           // composer.json文件
└── vendor                  // 依赖安装包
```

主要区别在于，对于不放置PHP源代码的目录，全部改用小写。

## 附录：对照表
 
### 附录1：类对照表

以下是PhalApi 2.x 与PhalApi 1.x 的类对照关系。  

PhalApi v2.x |PhalApi v1.x |备注
---|---|---
PhalApi\PhalApi|PhalApi|
PhalApi\Api|PhalApi_Api|
PhalApi\ApiFactory|PhalApi_ApiFactory|
PhalApi\Cache|PhalApi_Cache|
PhalApi\Cache\APCUCache|PhalApi_Cache_APCU|
PhalApi\Cache\FileCache|PhalApi_Cache_File|
PhalApi\Cache\MemcacheCache|PhalApi_Cache_Memcache|
PhalApi\Cache\MemcachedCache|PhalApi_Cache_Memcached|
PhalApi\Cache\MultiCache|PhalApi_Cache_Multi|
PhalApi\Cache\NoneCache|PhalApi_Cache_None|
PhalApi\Cache\RedisCache|PhalApi_Cache_Redis|
PhalApi\Config|PhalApi_Config|
PhalApi\Config\FileConfig|PhalApi_Config_File|
PhalApi\Config\YaconfConfig|PhalApi_Config_Yaconf|
PhalApi\Cookie|PhalApi_Cookie|
PhalApi\Cookie\MultiCookie|PhalApi_Cookie_Multi|
PhalApi\Crypt|PhalApi_Crypt|
PhalApi\Crypt\McryptCrypt|PhalApi_Crypt_Mcrypt|
PhalApi\Crypt\MultiMcryptCrypt|PhalApi_Crypt_MultiMcrypt|
PhalApi\Crypt\RSA\KeyGenerator|PhalApi_Crypt_RSA_KeyGenerator|
PhalApi\Crypt\RSA\MultiBase|PhalApi_Crypt_RSA_MultiBase|
PhalApi\Crypt\RSA\MultiPri2PubCrypt|PhalApi_Crypt_RSA_MultiPri2Pub|
PhalApi\Crypt\RSA\MultiPub2PriCrypt|PhalApi_Crypt_RSA_MultiPub2Pri|
PhalApi\Crypt\RSA\Pri2PubCrypt|PhalApi_Crypt_RSA_Pri2Pub|
PhalApi\Crypt\RSA\Pub2PriCrypt|PhalApi_Crypt_RSA_Pub2Pri|
PhalApi\CUrl|PhalApi_CUrl|
PhalApi\Database|PhalApi_DB|
PhalApi\Database\NotORMDatabase|PhalApi_DB_NotORM|改用Database全称
PhalApi\DependenceInjection|PhalApi_DI|改用DependenceInjection全称
PhalApi\Exception|PhalApi_Exception|
PhalApi\Exception\BadRequestException|PhalApi_Exception_BadRequest|
PhalApi\Exception\InternalServerErrorException|PhalApi_Exception_InternalServerError|
PhalApi\Exception\RedirectException|PhalApi_Exception_Redirect|
PhalApi\Filter|PhalApi_Filter|
PhalApi\Filter\NoneFilter|PhalApi_Filter_None|
PhalApi\Filter\SimpleMD5Filter|PhalApi_Filter_SimpleMD5|
PhalApi\Helper\ApiDesc|PhalApi_Helper_ApiDesc|应用层不用关注
PhalApi\Helper\ApiList|PhalApi_Helper_ApiList|应用层不用关注
PhalApi\Helper\ApiOnline|PhalApi_Helper_ApiOnline|应用层不用关注
PhalApi\Helper\TestRunner|PhalApi_Helper_TestRunner|
PhalApi\Helper\Tracer|PhalApi_Helper_Tracer|
PhalApi\Loader|PhalApi_Loader|
PhalApi\Logger|PhalApi_Logger|
PhalApi\Logger\ExplorerLogger|PhalApi_Logger_Explorer|
PhalApi\Logger\FileLogger|PhalApi_Logger_File|
PhalApi\Model|PhalApi_Model|
PhalApi\Model\NotORMModel|PhalApi_Model_NotORM|
PhalApi\Model\Proxy|PhalApi_ModelProxy|
PhalApi\Model\Query|PhalApi_ModelQuery|
PhalApi\Request|PhalApi_Request|
PhalApi\Request\Formatter|PhalApi_Request_Formatter|
PhalApi\Request\Formatter\ArrayFormatter|PhalApi_Request_Formatter_Array|应用层不用关注
PhalApi\Request\Formatter\BaseFormatter|PhalApi_Request_Formatter_Base|应用层不用关注
PhalApi\Request\Formatter\BooleanFormatter|PhalApi_Request_Formatter_Boolean|应用层不用关注
PhalApi\Request\Formatter\CallableFormatter|PhalApi_Request_Formatter_Callable|应用层不用关注
PhalApi\Request\Formatter\CallbackFormatter|PhalApi_Request_Formatter_Callback|应用层不用关注
PhalApi\Request\Formatter\DateFormatter|PhalApi_Request_Formatter_Date|应用层不用关注
PhalApi\Request\Formatter\EnumFormatter|PhalApi_Request_Formatter_Enum|应用层不用关注
PhalApi\Request\Formatter\FileFormatter|PhalApi_Request_Formatter_File|应用层不用关注
PhalApi\Request\Formatter\FloatFormatter|PhalApi_Request_Formatter_Float|应用层不用关注
PhalApi\Request\Formatter\IntFormatter|PhalApi_Request_Formatter_Int|应用层不用关注
PhalApi\Request\Formatter\StringFormatter|PhalApi_Request_Formatter_String|应用层不用关注
PhalApi\Request\Parser|PhalApi_Request_Var|重命名为Parser，避免与关键字var冲突
PhalApi\Response|PhalApi_Response|
PhalApi\Response\ExplorerResponse|PhalApi_Response_Explorer|
PhalApi\Response\JsonResponse|PhalApi_Response_Json|
PhalApi\Response\JsonpResponse|PhalApi_Response_JsonP|注意p字母为小写
PhalApi\Response\XmlResponse|PhalApi_Response_Xml|
PhalApi\Tool|PhalApi_Tool|
PhalApi\Translator|PhalApi_Translator|



#### 类名重命名规则

原来的类名遵循PEAR规范，现需要调整遵循PSR-4规范。如：  

```
原来的：PhalApi_Filter

调整后：\PhalApi\Filter  
```

对于有继承的情况，为了避免最后的关键字有冲突，统一在子类后面添加父类的名称作为后续。如：  
```
原来的：
PhalApi_COnfig_File
PhalApi_COnfig_Yaconf

调整后：
PhalApi\Config\FileConfig
PhalApi\Config\YaconfConfig
```

### 附录2：函数对照表

以下是PhalApi 2.x 与PhalApi 1.x 的函数对照关系。  

PhalApi v2.x |PhalApi v1.x |备注
---|---|---
PhalApi\DI()|DI()|
PhalApi\SL()|SL()|
PhalApi\T()|T()|

函数名保持一致，但需要注意前面添加PhalApi命名空间前缀。  

### 附录3：脚本命令对照表

以下是PhalApi 2.x 与PhalApi 1.x 的脚本命令对照关系。  

PhalApi v2.x |PhalApi v1.x |备注
---|---|---
./bin/phalapi-buildsqls|./PhalApi/phalapi-buildsqls|生成SQL语句
./bin/phalapi-buildtest|./PhalApi/phalapi-buildtest|生成测试骨架代码
暂未迁移|./PhalApi/phalapibuildcode|创建项目代码
暂未迁移|./PhalApi/phalapi-buildapp|创建新项目，暂不需要迁移

### 附录4：可访问入口对照表

以下是PhalApi 2.x 与PhalApi 1.x 的可访问入口对照关系。  

PhalApi v2.x |PhalApi v1.x |备注
---|---|---
./public/index.php|./Public/demo/index.php|可省略index.php文件
./public/docs.php|./Public/demo/listAllApis.php|在线接口列表文档
./public/docs.php?detail=1|./Public/demo/checkApiParams.php|在线接口详情文档，通过detail参数区分
./public/docs/|./Public/demo/docs/|离线文档生成目录

