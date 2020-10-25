# 日志

关于日志接口，PSR规范中给出了相当好的说明和定义，并且有多种细分的日记级别。  

![](http://cdn7.phalapi.net//ch-2-logger-interface.png) 


## 简化版的日记接口

虽然PSR规范中详尽定义了日志接口，然而在用使用开源框架或内部框架进行项目开发过程中，实际上日记的分类并没有使用得那么丰富，通常只是频繁集中在某几类。为了减少不必要的复杂性，PhalApi特地将此规范的日志接口精简为三种，只有：  

 + **error**： 系统异常类日记
 + **info**： 业务纪录类日记
 + **debug**： 开发调试类日记

### error 系统异常类日记

系统异常类日志用于纪录**在后端不应该发生却发生的事情**，即通常所说的系统异常。例如：调用第三方、的接口失败了，此时需要纪录一下当时的场景，以便复查和定位出错的原因。又如：写入一条纪录到数据纪录却失败了，此时需要纪录一下，以便进一步排查。  
  
纪录系统异常日志，用法很简单。可以使用[PhalApi\Logger::error($msg, $data)](https://github.com/phalapi/kernal/blob/master/src/Logger.php#L111)接口，第一个参数$msg用于描述日志信息，第二个可选参数为上下文场景的信息。下面是一些使用示例。     
```php
// 只有描述
\PhalApi\DI()->logger->error('fail to insert DB');

// 描述 + 简单的信息
\PhalApi\DI()->logger->error('fail to insert DB', 'try to register user dogstar');

// 描述 + 当时的上下文数据
$data = array('name' => 'dogstar', 'password' => '123456');
\PhalApi\DI()->logger->error('fail to insert DB', $data);
```

上面三条纪录，会在日记文件中生成类似以下的日志内容。  
```bash
$ tailf ./runtime/log/201502/20150207.log 
2015-02-07 20:37:55|ERROR|fail to insert DB
2015-02-07 20:37:55|ERROR|fail to insert DB|try to register user dogstar
2015-02-07 20:37:55|ERROR|fail to insert DB|{"name":"dogstar","password":"123456"}
```

### info 业务纪录类日记

业务纪录日志，是指纪录业务上关键流程环节的操作，以便发生系统问题后进行回滚处理、问题排查以及数据统计。如在有缓存的情况下，可能数据没及时写入数据库而导致数据丢失或者回档，这里可以通过日记简单查看是否可以恢复。以及说明一下操作发生的背景或原由，如通常游戏中用户的经验值添加：  
```php
// 假设：10 + 2 = 12
\PhalApi\DI()->logger->info('add user exp', array('name' => 'dogstar', 'before' => 10, 'addExp' => 2, 'after' => 12, 'reason' => 'help one more phper'));
```

对应的日记为：
```
2015-02-07 20:48:51|INFO|add user exp|{"name":"dogstar","before":10,"addExp":2,"after":12,"reason":"help one more phper"}
```  

### debug 开发调试类日记

开发调试类日记，主要用于开发过程中的调试。用法如上，这里不再赘述。以下是一些简单的示例。  
```php
// 只有描述
\PhalApi\DI()->logger->debug('just for test');

// 描述 + 简单的信息
\PhalApi\DI()->logger->debug('just for test', '一些其他的描述 ...');

// 描述 + 当时的上下文数据
\PhalApi\DI()->logger->debug('just for test', array('name' => 'dogstar', 'password' => '******'));
```

## 更灵活的日志分类  

若上面的error、info、debug都不能满足项目的需求时，可以使用[PhalApi\Logger::log($type, $msg, $data)](https://github.com/phalapi/kernal/blob/master/src/Logger.php#L75)接口进行更灵活的日记纪录。  
```php
\PhalApi\DI()->logger->log('demo', 'add user exp', array('name' => 'dogstar', 'after' => 12));
\PhalApi\DI()->logger->log('test', 'add user exp', array('name' => 'dogstar', 'after' => 12));
```

对应的日记为：
```
2015-02-07 21:13:27|DEMO|add user exp|{"name":"dogstar","after":12}
2015-02-07 21:15:39|TEST|add user exp|{"name":"dogstar","after":12}
```
  
注意到，第一个参数为日记分类的名称，在写入日记时会自动转换为大写。其接口函数签名为：  
```php
    /**
     * 日记纪录
     *
     * 可根据不同需要，将日记写入不同的媒介
     *
     * @param string $type 日记类型，如：info/debug/error, etc
     * @param string $msg 日记关键描述
     * @param string/array $data 场景上下文信息
     * @return NULL
     */
    abstract public function log($type, $msg, $data);
```

## 指定日志级别

在使用日志纪录前，在注册日志```\PhalApi\DI()->logger```服务时须指定开启的日志级别，以便允许指定级别的日志得以纪录，从而达到选择性保存所需要的日志的目的。  

通过[PhalApi\Logger](https://github.com/phalapi/kernal/blob/master/src/Logger.php)的构造函数的参数，可以指定日志级别。多个日记级别使用或运算进行组合。  
```php
// 日记纪录
$di->logger = new FileLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
```

上面的三类日记分别对应的标识如下。  

日志类型|日志级别标识
---|---
error 系统异常类|PhalApi\Logger::LOG_LEVEL_ERROR
info 业务纪录类|PhalApi\Logger::LOG_LEVEL_INFO
debug 开发调试类|PhalApi\Logger::LOG_LEVEL_DEBUG

## 文件日记服务

PhalApi框架主要使用了文件进行日记纪录，因此这里重点介绍文件日记的创建和使用。创建日记文件服务的方式非常灵活，可以有三种方式。  
 + 方式1：直接通过new实例化PhalApi\Logger\FileLogger对象
 + 方式2：通过工厂方法PhalApi\Logger\FileLogger::create()创建
 + 方式3：结合配置和工厂方法，通过配置方式创建

下面分别介绍。  

### 方式1：直接new创建文件日记服务
在./config/di.php文件中，直接使用new创建，这也是PhalApi 2.x早期版本的写法。  
```php
use PhalApi\Logger\FileLogger;

// 日记纪录
$di->logger = new FileLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
```

对于文件日记服务的构造函数的参数，依次是：  
 + **$logFolder** 必须，string类型，日记目录，需要使用已存在且有写入权限的绝对目录路径
 + **$level** 必须，int类型，使用位运算组合，需要纪录的日记级别，如：```Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR```
 + **$dateFormat** string类型，时间日期格式，默认是：```Y-m-d H:i:s```
 + **$debug** boolean类型，是否调试，默认与DI的调试保持一致
 + **$filePrefix** string类型，文件名前缀，必须为有效的文件名组成部分，自动使用下划线连接系统文件名，默认为空
 + **$separator** string类型，日记内容分隔符，默认为|

最终保存的日记文件路径，由以下部分组成：
```
{$logFolder}/log/{$当前年月Ym}/[{$filePrefix}_]{$当前年月日}.log
```
例如默认下的示例：  
```
/path/to/phalapi/runtime/log/202002/20200220.log
```
若设置filePrefix文件前缀为```app```，则对应的保存路径为：
```
/path/to/phalapi/runtime/log/202002/app_20200220.log
```

### 方式2：通过工厂方法创建文件日记服务
通过方式1使用new方法创建，有个小缺点是，由于构造函数参数过多，容易造成混淆。因此，也可以使用PhalApi\Logger\FileLogger::create()的工厂方法，根据数组配置来创建。

```php
use PhalApi\Logger\FileLogger;

$fileLoggerConfig = array(
    'log_folder' => API_ROOT . '/runtime',  // 日记目录，需要使用已存在且有写入权限的绝对目录路径
    'level' => 7,                           // 需要纪录的日记级别，默认：Logger::LOG_LEVEL_DEBUG(1) | Logger::LOG_LEVEL_INFO(2) | Logger::LOG_LEVEL_ERROR(4)
    'date_format' => 'Y-m-d H:i:s',         // 时间日期格式
    'debug' => NULL,                        // 是否调试，文件日记服务独有的调度开关，为NULL时默认跟随DI的调试模式
    'file_prefix' => '',                    // 文件名前缀，必须为有效的文件名组成部分，自动使用下划线连接系统文件
    'separator' => "|",                     // 日记内容分隔符，如：\t，注意使用双引号保持转义
);
$di->logger = FileLogger::create($fileLoggerConfig);
```
可以看到，数组配置方式更灵活，对于不需要用到的配置，可以忽略。每个配置项与构造函数参数保持一致，只是配置项名称采用蛇形格式。

### 方式3：结合工厂方法和配置进行创建文件日记服务
方式3是方式2的升级版，将文件日记服务需要的配置提取到./config/sys.php系统配置文件进行统一管理和维护，从而使得项目后期只需要修改配置即可调整所需要的日记服务功能。 

在./config/sys.php系统配置文件中添加：  
```php
    /**
     * 文件日记
     */
    'file_logger' => array(
        'log_folder' => API_ROOT . '/runtime',  // 日记目录，需要使用已存在且有写入权限的绝对目录路径
        'level' => 7,                           // 需要纪录的日记级别，默认：Logger::LOG_LEVEL_DEBUG(1) | Logger::LOG_LEVEL_INFO(2) | Logger::LOG_LEVEL_ERROR(4)
        'date_format' => 'Y-m-d H:i:s',         // 时间日期格式
        'debug' => NULL,                        // 是否调试，文件日记服务独有的调度开关，为NULL时默认跟随DI的调试模式
        'file_prefix' => '',                    // 文件名前缀，必须为有效的文件名组成部分，自动使用下划线连接系统文件
        'separator' => "|",                     // 日记内容分隔符，如：\t
    ),
```
随后在./config/di.php文件中，注册文件日记服务时的代码调整为：  
```php
use PhalApi\Logger\FileLogger;

// 日记纪录
$di->logger = FileLogger::create($di->config->get('sys.file_logger'));
```

> 温馨提示：PhalApi 2.11.0 及以上版本采用方式3写法。  

#### 如何创建多个文件日记服务？
什么情况下需要多个文件日记服务呢？当初始化的配置不同时，可考虑采用多个文件日记服务。例如需要保存到不同前缀日记文件时。
  
参考方式3，可快速创建多个文件日记服务。首先在./config/sys.php追加多一份配置，例如：  
```php
    /**
     * 文件日记
     */
    'file_logger' => array(
        'log_folder' => API_ROOT . '/runtime',  // 日记目录，需要使用已存在且有写入权限的绝对目录路径
        'level' => 7,                           // 需要纪录的日记级别，默认：Logger::LOG_LEVEL_DEBUG(1) | Logger::LOG_LEVEL_INFO(2) | Logger::LOG_LEVEL_ERROR(4)
        'date_format' => 'Y-m-d H:i:s',         // 时间日期格式
        'debug' => NULL,                        // 是否调试，文件日记服务独有的调度开关，为NULL时默认跟随DI的调试模式
        'file_prefix' => '',                    // 文件名前缀，必须为有效的文件名组成部分，自动使用下划线连接系统文件
        'separator' => "|",                     // 日记内容分隔符，如：\t
    ),
    /**
     * 文件日记-app
     */
    'file_logger_app' => array(
        'log_folder' => API_ROOT . '/runtime',  // 日记目录，需要使用已存在且有写入权限的绝对目录路径
        'level' => 7,                           // 需要纪录的日记级别，默认：Logger::LOG_LEVEL_DEBUG(1) | Logger::LOG_LEVEL_INFO(2) | Logger::LOG_LEVEL_ERROR(4)
        'date_format' => 'Y-m-d H:i:s',         // 时间日期格式
        'debug' => NULL,                        // 是否调试，文件日记服务独有的调度开关，为NULL时默认跟随DI的调试模式
        'file_prefix' => 'app',                    // 文件名前缀，必须为有效的文件名组成部分，自动使用下划线连接系统文件
        'separator' => "|",                     // 日记内容分隔符，如：\t
    ),
```

随后在./config/di.php文件中，使用新的配置注册新的文件日记服务。服务名称可自行指定。 
```php
use PhalApi\Logger\FileLogger;

// 日记纪录
$di->logger = FileLogger::create($di->config->get('sys.file_logger'));
// 日记纪录-app
$di->logger_app = FileLogger::create($di->config->get('sys.file_logger_app'));
```

最后，在需要的地方使用```\PhalApi\DI()->logger_app```。

### 关于文件日记的补充说明
自从PhalApi 2.8.0及上以版本开始，区分隐式静默和显式异常两种模式，可通过\PhalApi\DI()->debug全局模式或初始化时指定调试模式。为调试模式时，若写入失败将500异常提示；为非调试模式（即生产环境时），若写入失败则忽略，不抛异常，也不出现warnging提示，以免影响接口的正常返回。

## 扩展：定制你的日志

普遍情况下，我们认为将日记存放在文件是比较合理的，因为便于查看、管理和统计。当然，如果你的项目需要将日记纪录保存在其他存储媒介中，也可以快速扩展实现的。例如实现数据库的存储思路。   
```php
<?php
namespace App\Common\Logger;

use PhalApi\Logger;

class DBLogger extends Logger {
    
    public function log($type, $msg, $data) {
        // TODO 数据库的日记写入 ...
    } 
}
```

随后，重新注册```\PhalApiDI()->logger```服务即可。  
```php
$di->logger = new App\Common\Logger\DBLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
```
