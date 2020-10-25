# 数据库分库分表策略

也许，大家会觉得PhalApi对于NotORM的封装略过于复杂，但这样设计的初衷以及好处是能快速实现分库分表策略。这一策略能在海量数据和高并发访问下是非常行之有效的。

所以，这一章，我们将进入学习如何在PhalApi中配置数据库分库分表策略，以及如何自动生成分表的SQL变更语句。

我们先来看分表（一个数据库内，分多张表），再来看分库（多个数据库）。


## 分表策略配置

假设有以下多个数据库表，它们的表结构一样。  

数据库表|数据库实例
---|---
tbl_demo|db_master
tbl_demo_0|db_master
tbl_demo_1|db_master
tbl_demo_2|db_master

为了使用分表存储，可以修改数据库表的配置，让它支持分表的情况。  
```php
return array(
    'tables' => array(    
        'demo' => array(                            // 表名，不带表前缀，不带分表后缀
            'prefix' => '',                         // 当前的表名前缀
            'key' => 'id',                          // 当前的表主键名
            'map' => array(                         // 当前的分表存储路由配置
                array('db' => 'db_master'),         // 单表配置：array('db' => 服务器标记)
                array('start' => 0, 'end' => 2, 'db' => 'db_master'),     // 三张分表的配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
                ),
            ),
    ),
);
```
上面配置map选项中```array('db' => 'master')```用于指定缺省主表使用master数据库实例，而下一组映射关系则是用于配置连续在同一台数据库实例的分表区间，即tbl_demo_0、tbl_demo_1、tbl_demo_2都使用了master数据库实例。

### map配置详解

在配置分表时，map配置是关键的配置。可以配置多组，通常配置的顺序是：

 + 默认的非分表配置
 + 从0开始的前N个分表配置
 + 从N+1到……的分表配置

例如，默认的非分表配置，主要是配置使用哪个数据库，通过数据库标识（如默认的：db_master）指定。

```php
'map' => array(
    array('db' => 'db_master'),
),
```

接下来是分表的配置，分表的下标通常从0开始，这取决于你在Model子类中的分表策略。你也可以从1001开始，可以从任意数字开始。在配置过程中，主要能保证的分表连续性即可。

例如，对于上面的分表配置，我们还可以这样配置，效果是一样的。

一个极端的方式，分别配置分表tbl_demo_0、tbl_demo_1、tbl_demo_2，即各张表配置一个策略：
```php
'map' => array(
    array('start' => 0, 'end' => 0, 'db' => 'db_master'),
    array('start' => 1, 'end' => 1, 'db' => 'db_master'),
    array('start' => 2, 'end' => 2, 'db' => 'db_master'),
),
```

此外，也可以配置两组。分别配置分表tbl_demo_0和tbl_demo_1，以及tbl_demo_2（前2后1）：
```php
'map' => array(
    array('start' => 0, 'end' => 1, 'db' => 'db_master'),
    array('start' => 2, 'end' => 2, 'db' => 'db_master'),
),
```

当然，也可以前1后2，即第一个库1张分表，第二个库2张分表：
```php
'map' => array(
    array('start' => 0, 'end' => 0, 'db' => 'db_master'),
    array('start' => 1, 'end' => 2, 'db' => 'db_master'),
),
```

关键点再重复说明一下，要保证map中分表后缀的连续性。

> 温馨提示：当分表找不到时，PhalApi会自动退化使用缺省主表，即去掉分表后缀的表名。例如tbl_demo_0找不到则退化为tbl_demo。  

由此，推论出另一外需要特别注意的点。

### 推论：如果不需要分表，禁止在表名添加 **下划线+数字** 后缀。

例如，不要这么设计表名：
 
 + tbl_user_1
 + user_1
 + user_20190101

可以改为去掉下划线或者再加个字母作为后缀，例如改为：

 + tbl_user
 + user_1_bak
 + user_20190101_tag


### 如果表名确实需要保留 **下划线+数字** 后缀怎么办？


进行数据库查询时，以下划线+数字为后缀的表名会自动作为分表被解析，当分表策略不存在时会自动去掉数字后缀。
那么，在项目中如果确实需要保留 **下划线+数字** 后缀怎么办？  

自从PhalApi 2.12.0 及以上版本后，通过新增的```dbs.tables.__default__.keep_suffix_if_no_map```配置项，当设置为true时可以在当分表未匹配时依然保留数字作为表后缀。分表路由中也可通过```keep_suffix_if_no_map```进行配置，且优先级高于```__default__```，同时能进行单独配置。  

以下是对应的数据库配置：  
```php
    'tables' => array(
        // 通用路由
        '__default__' => array(                     // 固定的系统标志，不能修改！
            'prefix' => '',                         // 数据库统一表名前缀，无前缀保留空
            'key' => 'id',                          // 数据库统一表主键名，通常为id
            'keep_suffix_if_no_map' => true,        // 当分表未匹配时依然保留数字作为表后缀
            'map' => array(                         // 数据库统一默认存储路由
                array('db' => 'db_master'),         // db_master对应前面servers.db_master配置，须对应！
                ),
            ),
        ),
```

> keep_suffix_if_no_map配置项，需要PhalApi 2.12.0 及以上版本方可支持。

### Model子类实现分表逻辑

假设分表的规则是根据ID对3进行求余。当需要使用分表时，在使用Model基类的情况下，可以通过重写```PhalApi\Model\NotORMModel::getTableName($id)```实现相应的分表规则。  
```php
<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Demo extends NotORM {

    protected function getTableName($id) {
        $tableName = 'demo';
        if ($id !== null) {
            $tableName .= '_' . ($id % 3);
        }
        return $tableName;
    }
}
```

然后，便可使用之前一样的CURD基本操作，但框架会自动匹配分表的映射。例如：    
```php
$model = new App\Model\Demo();

$row = $model->get('3', 'id');   // 使用分表tbl_demo_0
$row = $model->get('10', 'id');  // 使用分表tbl_demo_1
$row = $model->get('2', 'id');   // 使用分表tbl_demo_2
```

回到使用Model基类的上下文，更进一步，我们可以通过```$this->getORM($id)```来获取分表的实例从而进行分表的操作。如：  
```php
<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Demo extends NotORM {

    public function getNameById($id) {
        $row = $this->getORM($id)->select('name')->fetchRow();
        return !empty($row) ? $row['name'] : '';
    }
}
```
通过传入不同的$id，即可获取相应的分表实例。  

至此，整体的架构总结如下：

![](http://cdn7.okayapi.com/yesyesapi_20190420122044_1832a5d09987133f180d4464f11b7f25.jpeg)

回顾前面学的知识点，获取NotORM实例有两种方式：

 + 全局获取方式
 + 局部获取方式

之所以强烈推荐使用局部获取方式，不仅是封装所带来的好处，更在于当配置了分表策略时，能更好地统一管控，避免过高的、人为的偶然复杂性。而这些技术债务，可以通过约定统一使用局部获取方式，在一开始就避免。

### 自动生成SQL建表语句

把数据库表的基本建表语句保存到./data目录下，文件名与数据库表名相同，后缀统一为“.sql”。如这里的./data/demo.sql文件。  

```sql
`name` varchar(11) DEFAULT NULL,
```

需要注意的是，这里说的基本建表语句是指：仅是这个表所特有的字段，排除已固定公共有的自增主键id、扩展字段ext_data和CREATE TABLE关键字等。  

然后可以使用phalapi-buildsqls脚本命令，快速自动生成demo缺省主表和全部分表的建表SQL语句。如下： 
```bash
$ ./bin/phalapi-buildsqls ./config/dbs.php demo
```  

正常情况下，会生成类似以下的SQL语句：  
```sql
CREATE TABLE `demo` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(11) DEFAULT NULL,
    `ext_data` text COMMENT 'json data here',
     PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
CREATE TABLE `tpl_demo_0` ... ...;
CREATE TABLE `tpl_demo_1`  ... ...;
CREATE TABLE `tpl_demo_2`  ... ...;
```

在将上面的SQL语句导入数据库后，或者手动创建数据库表后，便可以像之前那样操作数据库了。

## 分库配置策略

在了解了分表策略后，再来了解分库配置策略就非常简单了。

分库是指，同一张表，不仅在逻辑上有分表（前面是配置在同一个数据库内），还可以在物理存储上存放在多个数据库服务器中。

例如对于日记表，我们可以配置100张分表，并且存放在两个数据库服务器上。也就是：

 + 前面50张log日志表，tbl_log_0 ~ tbl_log_49，存在第一个数据库db_log_first
 + 后面50张log日志表，tbl_log_50 ~ tbl_log_99，存在第二个数据库db_log_second

首先，通过./config/dbs.php的简单配置，就能实现连接多个数据库。假设我们有两个数据库：

 + 第一个数据库：db_log_first
 + 第二个数据库：db_log_second

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
            'name'      => 'db_log_first',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '',                      //数据库密码
            'port'      => 3306,                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
        // 第二个数据库
        'db_ext' => array(                         //服务器标记
            'host'      => '192.168.1.100',             //数据库域名
            'name'      => 'db_log_second',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '',                      //数据库密码
            'port'      => 3306,                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
    ),

    // 略……
```

第二步，再继续配置，指定不同的数据库分表使用哪个数据库。可以这样配置：
```php
    'tables' => array(    
        //通用路由（默认的配置要保留，其他数据库表要用）
        '__default__' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_master'),
            ),
        ),

        
        'log' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
            	// 注意，是配置数据库标记，不是真实数据库名
                array('db' => 'db_master'), 

                // 前面50张log日志表：db_log_first.tbl_log_0 ~ db_log_first.tbl_log_49
                array('start' => 0, 'end' => 49, 'db' => 'db_master'),

                // 后面50张log日志表：db_log_second.tbl_log_50 ~ db_log_second.tbl_log_99
                array('start' => 50, 'end' => 99, 'db' => 'db_ext'),
            ),
        ),
    ),

```

上面配置，分别配置两组分表的策略。前面50张log日志表：db_log_first.tbl_log_0 ~ db_log_first.tbl_log_49，存在db_master这份数据库配置的数据库服务器中； 后面50张log日志表：db_log_second.tbl_log_50 ~ db_log_second.tbl_log_99则存在db_ext这个数据库标记的数据库服务器中。

最后，在Model层编写的代码和平时一样即可。不同的是，需要在获取NotORM实例时，指定哪张分表。

例如，可以为不同的用户存存储在不同的日志分表。根据user_id对100进行求余，可得到日志分表位置。

实现代码如下：

```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class Log extends NotORM {
    protected function getTableName($id) {
    	// 此时的id是user_id

        $tableName = 'log';
        if ($id !== null) {
            $tableName .= '_' . ($id % 100);
        }

        return $tableName;
    }

    public function countWhick($userId) {
        // 获取NotORM时指定userId
        return $this->getORM($userId)->count();
    }
}
```

当需要查user_id = 1的日志有多少条时，就可以这样写：

```php
$log = new \App\Model\Log();
$userLogCount = $log->count(1);

// 等效于：SELECT COUNT(*) FROM db_log_first.tbl_log_1
```

如果查user_id = 88的日志有多少条时，则可以这样写：

```php
$log = new \App\Model\Log();
$userLogCount = $log->count(88);

// 等效于：SELECT COUNT(*) FROM db_log_second.tbl_log_88
```

此时的整体架构图如下：

![](http://cdn7.okayapi.com/yesyesapi_20190420123932_fcfeeadf35b8b5cd63347888861a7fb4.jpeg)

恭喜你！又学习了分库分表的新技能！
