# 定制你的Model基类

如上面所介绍，在Model基类中，你可以完成很多事情，可以设置表名，可以指定使用哪个NotORM实例（在多数据库中特别有用），下面继续介绍更多高级的功能：如LOB序列化等。如果PhalApi现有的解决方案不能满足项目的需求，可作进行定制化处理。  

## 默认的Model基类与接口

PhalApi基于NotORM的Model基类是[PhalApi\Model\NotORMModel](https://github.com/phalapi/kernal/blob/master/src/Model/NotORMModel.php)，它主要有以下接口：

 + PhalApi\Model\NotORMModel::getTableName($id)，用于指定表名，或指定分表名
 + PhalApi\Model\NotORMModel::getTableKey($table)，根据表名获取主键名
 + PhalApi\Model\NotORMModel::getORM($id = NULL)，快速获取ORM实例，可用于切换数据库
 + PhalApi\Model\NotORMModel::formatExtData(&$data)，对LOB的ext_data字段进行格式化(序列化)
 + PhalApi\Model\NotORMModel::parseExtData(&$data)，对LOB的ext_data字段进行解析(反序列化)

下同分别介绍。

## 指定表名，指定分表名

这个特性，在前面数据库相关章节中已有介绍，这里再简单重温一下。

通常，框架会根据Model类名自动映射到表名。当需要手动指定表名时，可以这样写：
```php
<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    protected function getTableName($id) {
        return 'my_user';  // 手动设置表名为 my_user
    }
}
```

如果存在分表，那么可以自定义分表策略，即根据什么参考依据，分多少张表。例如前面根据user_id对10进行求余，得到的日志分表。实现代码如下：

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
  
## 根据表名获取主键名

并不是全部的表的主键名称都叫id，但我们希望能遵循这一国际惯例。

其次是分表处理，同样考虑到分表的情况，以及不同的表可能配置不同的主键表，而基于主键的CURD又必须要先知道表的主键名才能进行SQL查询。所以，问题就演变成了如何找到表的主键名。这里可以自动匹配，也可以手工指定。自动匹配是智能的，因为当我们更改表的主键时，可以自动同步更新而不需要担心遗漏（虽然这种情况很少发生）。手工指定可以大大减少系统不必要的匹配操作，因为我们开发人员也知道数据库的主键名是什么，但需要手工编写一些代码。在这里，提供了可选的手工指定，即可重写getTableKey($table)来指定你的主键名。
  
如，当user表的主键都为new_id时（希望不要真的发生）：
```php
class User extends NotORM {
    protected function getTableKey($table) {
        return 'new_id';
    }
}
```

## 快速获取ORM实例，可用于切换数据库

这里所说的获取ORM实例，是指局部获取NotORM的方式，与之对应的是全局获取方式。

如果需要切换不同的数据库，那么可以在这里统一控制。例如前面可以写一个基类，统一切换到MS Server数据库。

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

如果只有一个数据库，通常不用理会。当然，如果项目需要根据不同的场景切换数据库配置，除了可以在入口注册DI服务时使用不同配置外，也可以在这里切换。在这切换可以方便同时使用多个数据库。

## 序列化和反序列化

### （1）LOB序列化

先是LOB序列化，考虑到有分表的存在，当发生数据库变更时（特别在线上环境）会有一定的难度和风险，因此引入了扩展字段ext_data。当然，此字段也应对数据库变更的同时，也可以作为简单明了的值对象的大对象。序列化LOB首先要考虑的问题是使用二进制（BLOB）还是文本（CLOB），出于通用性、易读性和测试性，我们目前使用了json格式的文本序列化。所以，如果考虑到空间或性能问题（在少量数据下我认为问题不大，如果数据量大，应该及时重新调整数据库表结构），可以重写formatExtData() & parseExtData()。  
  
如改成serialize序列化：
```php
<?php
namespace App\Common;

abstract class MyNotORM extends \PhalApi\Model\NotORMModel {

    /**
     * 对LOB的ext_data字段进行格式化(序列化)
     */
    protected function formatExtData(&$data) {
        if (isset($data['ext_data'])) {
            $data['ext_data'] = serialize($data['ext_data']);
        }
    }

    /**
     * 对LOB的ext_data字段进行解析(反序列化)
     */
    protected function parseExtData(&$data) {
        if (isset($data['ext_data'])) {
            $data['ext_data'] = unserialize($data['ext_data'], true);
        }
    }

    // ...
}
```
  
将Model类继承于App\Common\MyNotORM后，  
```php
// $ vim ./app/Model/User.php

<?php
namespace App\Model;

class User extends \App\Common\NotORMModel {
   //...
}
```
就可以轻松切换到序列化，如：  
```php
$model = new \App\Model\User();

//带有ext_data的更新
$extData = array('level' => 3, 'coins' => 256);
$data = array('name' => 'test', 'update_time' => time(), 'ext_data' => $extData);
$model->update(1, $data); //基于主键的快速更新

```


