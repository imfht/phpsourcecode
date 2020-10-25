### **Len Framwork** 

   -  简介:
    
    这是个php框架, 但是这个框架还很弱小. Len 框架就像它的名字一样, 非常精简.
   

   - 目标: 
    
    简单! 易用! 没人用!


   - 说明:

    集成Composer组件; 集成Redis, Mongodb, Mysql, Yac 支持; 
    url仅支持pathinfo模式; 暂不提供模版引擎;
    
   - 适用场景
    
    独立脚本; web Api接口;

## 开始
#### Download:

```bash
    git clone https://gitee.com/libenfu/len.git
    cd len
```
#### 安装依赖:

```bash
    composer install --no-dev
```
#### 选择需要依赖:

```bash
    composer update --dev
```
#### 可能用到的组件:
[filp/whoops](https://packagist.org/packages/filp/whoops) PHP错误处理  
[firebase/php-jwt](https://packagist.org/packages/firebase/php-jwt) JWT(JSON Web Tokens) [more](https://jwt.io/)  
[endroid/qrcode](https://packagist.org/packages/endroid/qrcode) 用于生成二维码  
[geoip2/geoip2](https://packagist.org/packages/geoip2/geoip2) 地理位置分析  
[mongodb](https://packagist.org/packages/mongodb/mongodb) MongoDB抽象层  
[predis/predis](https://packagist.org/packages/predis/predis) Redis抽象层  
[phpunit/phpunit](https://packagist.org/packages/phpunit/phpunit) 单元测试  
[eaglewu/swoole-ide-helper](https://packagist.org/packages/eaglewu/swoole-ide-helper) Swoole IDE提示  
[kkguan/ide-helper](https://packagist.org/packages/kkguan/ide-helper) Yaf、Yar、Yac、Yaconf、Redis、Swoole等框架或C扩展代码自动补全  

#### 代码结构 :
<pre> 
├── app
|   └── controllers
│       └── Index.php
│
├── config
|   └──application.php
│ 
├── Len
│   ├── Cache
│   │   ├── CacheInterface.php
│   │   └── YacCache.php
│   │   
│   ├── Common
│   │   └── functions.php
│   │   
│   ├── DB
│   │   ├── MongodbModule.php
│   │   ├── MysqlModule.php
│   │   └── RedisModule.php
│   │   
│   ├── Input
│   │   ├── Argv.php
│   │   ├── Get.php
│   │   ├── Post.php
│   │   ├── Put.php
│   │   └── InputBase.php
│   │   
│   ├── JWT
│   │   └── Jwt.php
│   │   
│   ├── Lock
│   │   ├── FileLock.php
│   │   └── RedisLock.php
│   │   
│   ├── Output
│   │   ├── Error.php
│   │   ├── Json.php
│   │   ├── Xml.php
│   │   └── OutputBase.php
│   │   
│   ├── Utility
│   │   └── File.php
│   │   
│   ├── Xml
│   │   ├── Array2XML.php
│   │   └── XML2Array.php
│   │ 
│   ├── BaseControl.php
│   ├── BaseModel.php
│   ├── Core.php
│   ├── Dispatcher.php
│   ├── Len.php
│   ├── Logger.php
│   ├── Manager.php
│   ├── Router.php
│   └── Signal.php
|
├── public
│       └── index.php
|
├── .htaccess
│
└── composer.json

</pre>