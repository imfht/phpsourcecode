# 连接多个数据库

在其他情况下，项目需要连接多个数据库也是常见的需求。解决方案可以有多种，简单的方案，可以通过配置直接实现，但有一定局限性。复杂的方案，能解决更多应用场景遇到的问题并能更好满足约束限制。

这一章，将带你开启一段组合爆炸的神奇旅程。但本质就看实际有多少个数据库，以及最终有多少个NotORM实例。请记住这个经验法则：

** 一个数据库，对应一个NotORM实例；但一个NotORM实例可以对应多个数据库。**

## 简单方案：通过配置连接多个数据库

首先，通过./config/dbs.php的简单配置，就能实现连接多个数据库。

假设我们有两个数据库：

 + 第一个数据库：db_1
 + 第二个数据库：db_1

假设都是MySQL数据库，按前面介绍的格式，则可以在./config/dbs.php文件中配置：
```php
return array(
    /**
     * DB数据库服务器集群
     */
    'servers' => array(
        // 第一个数据库
        'db_master' => array(                         //服务器标记
            'host'      => '127.0.0.1',             //数据库域名
            'name'      => 'db_1',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '',                      //数据库密码
            'port'      => 3306,                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
        // 第二个数据库
        'db_other' => array(                         //服务器标记
            'host'      => '192.168.1.100',             //数据库域名
            'name'      => 'db_2',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '',                      //数据库密码
            'port'      => 3306,                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
    ),

    // 略……
```

第二步，再继续配置，不同的数据库表使用哪个数据库。参考分表配置的格式，只是这里是一个极端，即全部的分表只都有一张表，可以这样配置：
```php
    'tables' => array(    
         // 库表：db_1.user
        'user' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_master'),
            ),
        ),

        // 库表：db_2.log
        'log' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_other'),
            ),
        ),
    ),

```

上面配置，分别配置了user用户表用db_1，log日志表用db_2。其他依此类推。

最后，在Model层编写的代码和平时一样即可。

用户Model类：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function count() {
        // user表查db_1
        return $this->getORM()->count();
    }
}
```

日志Model类：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

// 另外的log表
class Log extends NotORM {
    public function count() {
        // log日记表查db_2
        return $this->getORM()->count();
    }
}
```

Model层代码编写和往常一样，但PhalApi框架会根据数据库路由配置自动实现不同数据库的连接。

至此，我们就可以通过配置来实现连接多个数据库。当前，整体架构如下：

![](http://cdn7.okayapi.com/yesyesapi_20190420121339_4c28ae473c5e932db3d31df18b901446.png)

但局限是：

 + 局限1：不同数据库不能有同名数据库表，否则会表名冲突。可以通过加前缀区分
 + 局限2：只支持PhalApi默认的数据库类型，例如：MySQL



### dblib_sqlserver连接支持

```
        /**
         * 增加pdo_dblib 和freetds 支持 mssql. 前提需支持 pdo_dblib，和freetds 
         * 1,通过phpinfo查看 PDO drivers中是否包含dblib 
         * 2,再终端输入命令测试本机是都可以正常链接你的数据库，比如： tsql -H 192.168.1.100  -p 1433 -U sa -P 1111 
         *   如果命令无效，则需要安装freetds
         * ref:https://www.php.net/manual/en/pdo.drivers.php
         */
        'db_sqlserver' => array(                       //服务器标记
            'type'      => 'dblib_sqlserver',         //数据库类型，暂时只支持：mysql, sqlserver(需要sqlsrv驱动)， dblib_sqlserver（需要pdo_dblib+freetds）
            'host'      => '192.168.1.100',             //数据库域名
            'name'      => 'dbname',               //数据库名字
            'user'      => 'sa',                  //数据库用户名
            'password'  => '12345678',                      //数据库密码
            'port'      => 1433,                    //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
```

> 以上配置，需要PhalApi 2.7.0 及以上版本支持。

## 复杂方案：支持任意多个不同数据库

PhalApi 2.x 使用的是[NotORM](http://www.notorm.com/)来进行数据库操作，而NotORM底层则是采用了PDO。目前，NotORM支持： MySQL, SQLite, PostgreSQL, MS SQL, Oracle (Dibi support is obsolete)。 

当需要支持多个数据库时，可以按以下步骤来实现，共分为两大部分。第一部分，实现其他数据库的连接；第二部分，实现多个数据库共存。

第一部分如下：

 + 第一步、每一个数据库，单独一份./config/dbs.php配置文件（可复制此文件，如：./config/dbs_2.php）
 + 第二步、继承[PhalApi\Database\NotORMDatabase::createPDOBy($dbCfg)](https://github.com/phalapi/kernal/blob/master/src/Database/NotORMDatabase.php)接口，并实现指定数据库PDO的创建和连接
 + 第三步、在./config/di.php文件中，为新的数据库连接注册新的notorm服务

接着，是第二部分：

 + 第四步、为新的数据库连接实现新的Model基类，继承并重载[PhalApi\Model\NotORMModel::getORM($id = NULL)](https://github.com/phalapi/kernal/blob/master/src/Model/NotORMModel.php)方法，返回第三步的notorm服务
 + 第五步、在Model层，在具体的Model子类中，继承第四步的基类
 + 第六步，完成，正常的数据库操作

如果只有一个数据库，但不是MySQL数据库，则只需要完成第一部分；如果有多个数据库，则需要完成第一部分和第二部分。下面通过一个示例来概括介绍。

先来提前预览整体的架构，方便全局把控和了解。

![](http://cdn7.okayapi.com/yesyesapi_20190420130646_b618a82ea0dd3ee3d930b5ac5a1bc2cd.jpeg)


假设，我们现在需要连接三个数据库，分别是：

数据库类型|数据库名称|数据库域名|数据库端口|数据库账号|数据库密码
---|---|---|---|---
MySQL|phalapi|192.168.1.1|3306|root|123456
Ms Server|phalapi_ms|192.168.1.2|1433|root|abcdef
postgreSQL|phalapi_pg|192.168.1.3|3306|root|abc123

为了能同时使得这三个数据库，第一步，为这三个数据库，分别准备以下配置三个dbs.php文件。

MySQL默认数据库的配置文件./config/dbs.php：
```php
<?php
return array(
    'servers' => array(
        'db_master' => array(                         //服务器标记
            'host'      => '192.168.1.1',             //数据库域名
            'name'      => 'phalapi',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '123456',                      //数据库密码
            'port'      => 3306,                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
    ),

    /**
     * 自定义路由表
     */
    'tables' => array(
        //通用路由
        '__default__' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_master'),
            ),
        ),
    ),
);

```

MS Server的数据库配置文件，由于PhalApi 2.x内置已支持MS Server的连接，因此创建配置文件./config/dbs_ms.php，并放置：
```php
<?php
return array(
    'servers' => array(
        'db_master' => array(                         //服务器标记
            'type'      => 'sqlsrv',                // 指定使用sqlsrv
            'host'      => '192.168.1.2',             //数据库域名
            'name'      => 'phalapi_ms',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => 'abcdef',                      //数据库密码
            'port'      => 1433,                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
    ),

    /**
     * 自定义路由表
     */
    'tables' => array(
        //通用路由
        '__default__' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_master'),
            ),
        ),
    ),
);

```

最后，是postgreSQL数据库的配置，在PhalApi 2.6.0 版本前，框架不支持此类型的数据库连接，需要创建配置文件./config/dbs_pg.php：并放置：
```php
<?php
return array(
    'servers' => array(
        'db_master' => array(                         //服务器标记
            'type'      => 'pgsql',                // 指定使用pgsql
            'host'      => '192.168.1.3',             //数据库域名
            'name'      => 'phalapi_pg',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => 'abc123',                      //数据库密码
            'port'      => 3306,                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
    ),

    /**
     * 自定义路由表
     */
    'tables' => array(
        //通用路由
        '__default__' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_master'),
            ),
        ),
    ),
);

```
到这里，第一步完成。

第二步是，进行不同数据库的连接。参考PHP官方手册[PHP: PDO - Manua](https://www.php.net/manual/en/book.pdo.php)，PDO可支持以下数据库的连接：

 + MySQL (PDO) 
 + MS SQL Server (PDO) 
 + PostgreSQL (PDO) 
 + SQLite (PDO) 
 + Oracle (PDO) 
 + Firebird (PDO) 
 + CUBRID (PDO)
 + IBM (PDO) 
 + Informix (PDO) 
 + ODBC and DB2 (PDO) 
 + 4D (PDO)

在PhalApi 2.5.0 版本后，可内置支持MySQL (PDO)、MS SQL Server (PDO)、PostgreSQL (PDO)，如果需要其他类型数据库的连接，则需要继承PhalApi\Database\NotORMDatabase::createPDOBy($dbCfg)接口，并实现指定数据库PDO的创建和连接。以PostgreSQL (PDO)为例，可以这样实现代码。创建./src/app/Common/MyPostgreDB.php文件，并放置以下代码。
```php
<?php
namespace App\Common;

use PhalApi\Database;
use PhalApi\Database\NotORMDatabase;

class MyPostgreDB extends NotORMDatabase {
    protected function createPDOBy($dbCfg)
    {
        $dsn = sprintf('%s:dbname=%s;host=%s;port=%d',
            $dsn = sprintf('pgsql:dbname=%s;host=%s;port=%d',
                $dbCfg['name'],
                isset($dbCfg['host']) ? $dbCfg['host'] : 'localhost',
                isset($dbCfg['port']) ? $dbCfg['port'] : 3306
            );
        );
        $charset = isset($dbCfg['charset']) ? $dbCfg['charset'] : 'UTF8';
        $pdo = new \PDO(
            $dsn,
            $dbCfg['user'],
            $dbCfg['password']
        );
        $pdo->exec("SET NAMES '{$charset}'");
        return $pdo;
    }
}

```

在完成这些准备工作后，就可以在./config/di.php文件中，注册这些不同的数据库实例。在./config/di.php文件中添加以下代码。

```php
// 数据操作 - 基于NotORM
$di->notorm = new NotORMDatabase($di->config->get('dbs'), $di->debug);

// 追加

// MS Server数据库
$di->notorm_ms = new NotORMDatabase($di->config->get('dbs_ms'), $di->debug);

// PostgreSQL数据库（切换成自己的新类）
$di->notorm_pg = new App\Common\MyPostgreDB($di->config->get('dbs_pg'), $di->debug);
```

下面进行第二部分，到了第四步，需要分别实现两个Model基类，分别用于MS Server数据库和PostgreSQL数据库。

首先，是MS Server数据库的Model基类，创建./src/app/Model/MSModelBase.php文件，代码如下：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel;

class MSModelBase extends NotORMModel {
    protected function getORM($id = NULL) {
        $table = $this->getTableName($id);
        return \PhalApi\DI()->notorm_ms->$table; // 注意这一行，改为：notorm_ms
    }
}
```

然后，对于PostgreSQL数据库也这类似这样，即添加./src/app/Model/PostgreModelBase.php文件，代码如下：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel;

class PostgreModelBase extends NotORMModel {
    protected function getORM($id = NULL) {
        $table = $this->getTableName($id);
        return \PhalApi\DI()->notorm_pg->$table; // 注意这一行，改为：notorm_pg
    }
}
```

当你完成到这里，恭喜你，离成功不远啦！剩下的就是使用，基本上和平常的Model使用是一样的。

第五步，在需要的Model子类中，继承对应的数据库基类。为方便区分，可以为不同的数据库划分不同的目录。例如，对于MS Server，创建目录./src/app/Model/MSServer。假设有一张user的表，则可以创建./src/app/Model/MSServer/User.php文件，放置代码：
```php
<?php
namespace App\Model\MSServer;
use App\Model\MSModelBase;

class User extends MSModelBase { // 注意，这里换成新的基类
    protected function getTableName($id) {
        return 'user';
    }
}
```

PostgreSQL和这类似，不再赘述。

最后一步，就可以正常使用啦。例如：

```php
<?php

class User extends MSModelBase { // 注意，这里换成新的基类
    protected function getTableName($id) {
        return 'user';
    }

    public function count() {
        return $this->getORM()->count();
    }
}
```

搞定，收工！

## 补充说明
对于PhalApi 2.8.0 及以上版本，框架进一步优化了多个数据库的支持。只需重载[PhalApi\Model\NotORMModel::getNotORM()](https://github.com/phalapi/kernal/blob/master/src/Model/NotORMModel.php)，就可以在Model实现一键切换数据库实例。  

例如，默认代码是：  
```php
protected function getNotORM() {
    return \PhalApi\DI()->notorm;
}
```

你可以在./config/di.php注入多个数据库后，例如前文提及的：```$di->notorm_ms```，和```$di->notorm_pg```，则可以：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel;

class MSModelBase extends NotORMModel {
    protected function getNotORM() {
        return \PhalApi\DI()->notorm_ms; // 切换数据库实例 
    }
}
```

相比之前Hard Code的方式，这样会更优雅。  

## 小结

在PhalApi中，数据库操作主要是基于NotORM来实现。而对于数据库的连接，以及对于分库分表，则可以通过配置或者自定义开发来扩展。这种组合是非常灵活、优雅且设计巧妙的。

与传统的框架不同的是，PhalApi天生就支持多个数据库、分表分库的配置。更多复杂的组合功能，可以在熟悉前面这些配置和策略后自由发挥。期待你的大作品！
