# 配置

## 配置文件说明

默认情况下，项目里会有以下几个配置文件：  
```bash
$ tree ./config/
./Config/
├── app.php
├── dbs.php
├── di.php
└── sys.php
```
其中app.php为项目应用配置；dbs.php为分布式存储的数据库配置；sys.php为不同环境下的系统配置。这三个文件都是用于存放配置信息，可通过```\PhalApi\DI()->config```进行读取。  

值得注意的是，```./config/di.php```文件则属于依赖注入配置文件，用于配置需在```\PhalApi\DI()```容器中注册的服务资源。

## 配置的简单读取
默认已注册配置组件服务，名称是```\PhalApi\DI()->config```。  
```php
// 配置
$di->config = new FileConfig(API_ROOT . '/config');
```
  
假设app.php配置文件里有：  
```php
return array(
    'version' => '1.1.1',
    'email' => array(
        'address' => 'chanzonghuang@gmail.com',
    );
);
```
  
可以分别这样根据需要获取配置：
```php
// app.php里面的全部配置
\PhalApi\DI()->config->get('app');                //返回：array( ... ... )

// app.php里面的单个配置
\PhalApi\DI()->config->get('app.version');        //返回：1.1.1

// app.php里面的多级配置
\PhalApi\DI()->config->get('app.email.address');  //返回：'chanzonghuang@gmail.com'
```
  
其他配置文件的读取类似，你也可以根据需要添加新的配置文件。  

## 读取失败与默认值

当一个配置不存在时，返回NULL。例如：  
```php
\PhalApi\DI()->config->get('app.not_found');      //返回：NULL
```

当需要指定默认值时，可通过第二个参数指定。例如：  
```php
\PhalApi\DI()->config->get('app.not_found', 404); //返回：404
```

### 关于文件配置的补充说明
自从PhalApi 2.8.0及上以版本开始，区分隐式静默和显式异常两种模式，可通过\PhalApi\DI()->debug全局模式或初始化时指定调试模式。为调试模式时，若配置不存在将500异常提示；非调试模式时（即生产环境时），若配置文件不存在则不会抛异常，也不会出现之前的Warning提示，以免影响接口的正常返回。

## 当前环境的配置文件

在./public/init.php初始化时，有以下宏定义： 
```php
// 运行模式，可以是：dev, test, prod// 运行模式，可以是：dev, test, prod
defined('API_MODE') || define('API_MODE', 'prod');
```

API_MODE有三个值，分别是：
 + dev表示开发模式，此时如果./config/sys_dev.php、./config/app_dev.php、./config/dbs_dev.php配置文件若存在，则会优先加载```*_dev.php```系列配置文件。
 + test表示测试模式，此时如果./config/sys_test.php、./config/app_test.php、./config/dbs_test.php配置文件若存在，则会优先加载```*_test.php```系列配置文件。
 + prod表示生产模式，则加载./config/sys.php、./config/app.php、./config/dbs.php配置文件。

如果你定义了其他运行模式，则会优化加载相应系列的配置文件。优先加载是指存在则加载，不存在则使用prod生产模式时的配置文件。  

另外，请注意：  
 + 1、若在初始化时更新配置目录，则配置目录会整体跟随变化
 + 2、如果项目中有使用sys.php、app.php、dbs.php以外的其他配置文件，也会受API_MODE模式影响
 + 3、API_MODE要区分大小写，对应需要优先加载的配置文件名后缀

通过以上配置，可以实现在不同环境（开发环境、测试环境、回归环境、灰度环境、生产环境）下的配置切换和替换，而不需要改动到同一份配置文件，减少代码冲突和配置覆盖。

如果你需要在当前配置文件中覆盖生产环境的配置，可以先include再修改数组配置，最后再返回。例如这样：  
```php
<?php
// ./config/app_dev.php 配置文件
$config = include(__FILE__) . '/app.php';

$config['service_whitelist'][] = 'Hello.World'; // 追加更多白名单

return $config;
```

> 温馨提示：API_MODE需要PhalApi 2.12.0 及以上版本支持。  

## 使用Yaconf扩展快速读取配置

Yaconf扩展需要PHP 7及以上版本，并且需要先安装Yaconf扩展。
> **温馨提示：**Yaconf扩展的安装请参考[laruence/yaconf](https://github.com/laruence/yaconf)。  
  
安装部署完成后，先重新注册```\PhalApi\DI()->config```：
```php
// 配置
$di->config = new PhalApi\Config\YaconfConfig();
```

然后，便和正常的配置一样使用。
```
// 相当于Yaconf::get("foo")
\PhalApi\DI()->config->get('foo');

//相当于Yaconf::has("foo")
\PhalApi\DI()->config->has('foo'); 
```
  
需要注意的是，使用Yaconf扩展与默认的文件配置的区别的是，配置文件的目录路径以及配置文件的格式。当然也可以把Yaconf扩展的配置目录路径设置到PhalApi的配置目录./config。  

## 扩展：其他配置读取方式

如果需要使用其他方式读取配置，可根据实情需要，实现[PhalApi\Config](https://github.com/phalapi/kernal/blob/master/src/Config.php)接口，然后在```./config/di.php```文件重新注册```\PhalApi\DI()->config```即可。  

例如数据库配置，这样可以支持不发布代码的情况下进行配置更改。  
