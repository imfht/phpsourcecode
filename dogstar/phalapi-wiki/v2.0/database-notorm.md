# 数据库与NotORM

PhalApi的Model层，如果主要是针对数据库，那么就有必要先来了解NotORM。因为PhalApi框架主要是使用了NotORM来操作数据库。

## PhalApi的Model和NotORM整体架构

首先，为避免混淆概念，我们先来看下PhalApi 2.x中的Model和NotORM整体架构。

![](http://cdn7.okayapi.com/yesyesapi_20190420101919_543639c3044d444b972f23c484885833.png)

当我们需要操作数据库时，主要分为三个步骤：连接数据库、实现数据库表操作、调用。

+ ** 第一步、连接数据库 **

如前面章节介绍，在./config/dbs.php文件中配置好数据库后，在./config/di.php注册PhalApi\DI()->notrom服务，就可以实现数据库连接。

对应上图的右上角部分，这时PhalApi\DI()->notrom是针对数据库的，一个notorm对应一个数据库。反之，如果有多个数据库，则需要注册多个不名称的notorm，后面会再介绍。

+ ** 第二步、实现数据库表操作 **

原则上，推荐一张表一个Model子类。Model子类需要继承[PhalApi\Model\NotORMModel](https://github.com/phalapi/kernal/blob/master/src/Model/NotORMModel.php)。PhalApi框架会根据类名会自动映射表名，你也可以通过PhalApi\Model\NotORMModel::getTableName($id)手动指定表名。

对应上图的App\Model\User示例，之所以加粗是表示我们这章会重点关注这一Model层的实现。这个示例对应数据库表的user用户表。

+ ** 第三步，使用 **

遵循实现和使用分离，当我们在Model层封装好数据库表的操作后，就可以提供给客户端使用了。通常Model层的调用方是Domain层，也就是PhalApi框架的ADM分层模式。

下面，将通过user表示例详细介绍。

## 实现一个Model子类

根据“一张表一个Model类”的原则，我们先来针对user表创建一个Model子类。假设，用户user表结构如下：

```sql
CREATE TABLE `tbl_user` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `note` varchar(45) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

当需要新增一个Model子类时，可以继承于PhalApi\Model\NotORMModel类，并放置在App\Model命名空间下。例如新增App\Model\User.php文件，并在里面放置以下代码。  
```php
<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
}
```

## 如何指定表名？

上面的App\Model\User类，自动匹配的表名为：user，加上配置前缀“tbl_”，完整的表名是：tbl_user。

默认表名的自动匹配规则是：取“\Model\”后面部分的字符全部转小写，并且在转化后会加上配置的表前缀。  

又如：  
```php
<?php
namespace App\Model\User;
use PhalApi\Model\NotORMModel as NotORM;

class Friends extends NotORM {
}
```
则类App\Model\User\Friends自动匹配的表名为```user_friends```。以下是2.x版本的一些示例：  

2.x 的Model类名|对应的文件|自动匹配的表名|自动添加表前缀的完整表名
---|---|---|---
App\Model\User|./src/app/Model/User.php|user|tbl_user
App\ModelUser\Friends|./src/app/Model/User/Friends.php|user_friends|tbl_user_friends
App\User\Model\Friends|./src/app/user/Model/Friends.php|friends|tbl_friends
App\User\Model\User\Friends|./src/app/user/Model/User/Friends.php|user_friends|tbl_user_friends


但在以下场景或者其他需要手动指定表名的情况，可以重写```PhalApi\Model\NotORMModel::getTableName($id)```方法并手动指定表名。  

 + 存在分表
 + Model类名不含有“Model_”
 + 自动匹配的表名与实际表名不符
 + 数据库表使用蛇形命名法而类名使用大写字母分割的方式
 
如，当Model_User类对应的表名为：my_user表时，可这样重新指定表名： 
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
其中，$id参数用于进行分表的参考主键，只有当存在分表时才需要用到。通常传入的$id是整数，然后对分表的总数进行求余从而得出分表标识。

即存在分表时，需要返回的格式为：表名称 + 下划线 + 分表标识。分表标识通常从0开始，为连续的自然数。  

## 简单：4个CURD基本操作

对于基本的Model子类，可以得到基本的数据库操作。以下示例演示了Model的基本CURD操作。

```php
$model = new App\Model\User();

// 查询
$row = $model->get(1);
$row = $model->get(1, 'id, name'); //取指定的字段
$row = $model->get(1, array('id', 'name')); //可以数组取指定要获取的字段

// 更新
$data = array('name' => 'test', 'update_time' => time());
$model->update(1, $data); //基于主键的快速更新

// 插入
$data = array('name' => 'phalapi');
$id = $model->insert($data);
//$id = $model->insert($data, 5); //如果是分表，可以通过第二个参数指定分表的参考ID

// 删除
$model->delete(1);
```

> 特别提醒！需要特别注意的是，继承于PhalApi\Model\NotORMModel的Model子类，只拥有基本的四个数据库操作：get/update/insert/delete。更多其他数据库操作须经过NotORM实例来进行。

显然，只有CURD这四个基本操作是满足不了项目对于数据库操作的需求，下面继续介绍更全面的数据库操作方式。但在继续深入前，我们需要先来了解如何获取NotORM实例。

## 如何获取NotORM实例？

NotORM是一个优秀的开源PHP类库，可用于操作数据库。PhalApi的数据库操作，主要是依赖此NotORM来完成，但PhalApi 2.x已经基于最初的NotORM升级成了[phalapi/notorm](https://github.com/phalapi/notorm)。  

> 参考：NotORM官网：[www.notorm.com](http://www.notorm.com/)。 

重要事情讲三遍，默认情况下， 

 + 在PhalApi中，全部数据库操作都要经过NotORM实例来进行
 + 在PhalApi中，全部数据库操作都要经过NotORM实例来进行
 + 在PhalApi中，全部数据库操作都要经过NotORM实例来进行

这意味着，不能直接通过Model子类来进行（除下面将说到的4个基本操作外，即get/insert/update/delete），这是一种委托组合而非继承关系。以下示例可以加深理解： 

```php
// 错误！不能直接通过Model实例来操作，并且不能在Model类外面实现
$model = new \App\Model\User();
$users = $model->select('*')->fetchAll();
```

正确，并推荐写法是：  
```php
// 正确&推荐！通过NotORM实例看你咯，并且在Model内部实现
namespace App\Model;

class User {
    public function fetchAllUsers() {
        return $this->getORM()->select('*')->fetchAll();
    }
}

$model = new new \App\Model\User();
$users = $model->fetchAllUsers();
```

Model层只是针对NotORM的一层代理，而非直接继承的关系。这样的好处是能方便我们灵活、快速切换不同的数据库操作类库。

那么，如何获取NotORM实例呢？  

在PhalApi中获取NotORM实例，有两种方式：

 + 全局获取方式，能在任何地方使用
 + 局部获取方式，只能在Model子类中使用（推荐此用法）

### 全局获取方式

第一种全局获取的方式，可以用于任何地方，使用DI容器中的全局notorm服务：```\PhalApi\DI()->notorm->表名```。

这是因为我们已经在初始化文件中注册了```\PhalApi\DI()->notorm```这一服务。继续在后面追加表名，就可以获取到NotORM实例了。如这里的：\PhalApi\DI()->notorm->user。 

全局获取的方式，是为了方便编写脚本，并且可以指定任意表名。例如查user表的总数：
```php
$num = \PhalApi\DI()->notorm->user->count();
```   

### 局部获取方式

第二种局部获取的方式，在继承PhalApi\Model\NotORMModel的子类中使用：```$this->getORM()```。

这只限于继承PhalApi\Model\NotORMModel的子类中，并且只能获取当前Model类指定表名的NotORM实例。例如前面的App\Model\User类只能获取\PhalApi\DI()->notorm->user。如取总数：

```php
class User extends NotORM {
    public function count() {
        // 局部获取
        $orm = $this->getORM()->count(); 
    }
}
```

如果你不想写Model类，可以直接使用第一种全局获取方式。但是，我们PhalApi推荐使用封装的第二种方式，并且下面所介绍的使用都是基于第二种快速方式。 

## 特别注意NotORM的状态！

特别注意！不管是全局获取，还是局部获取，NotORM实例是带状态的，如果需要再次查询、更新或者删除等，需要获取新的实例！  

下面演示了一个不清除状态、错误的使用示例。

```php
// 获取一个NotORM实例
$orm = \PhalApi\DI()->notorm->user;

// 先带条件查一次
// SELECT * FROM tbl_user WHERE id = 1
$user1 = $orm->where('id', 1)->fetchOne();

// 再查一次
// 注意！此时where条件会叠加！！
// SELECT * FROM tbl_user WHERE id = 1 AND id = 2
$user2 = $orm->where('id', 2)->fetchOne();

```

正确写法是每次获取一个新的notorm实例：

```php
$user1 = \PhalApi\DI()->notorm->user->where('id', 1)->fetchOne();

$user2 = \PhalApi\DI()->notorm->user->where('id', 2)->fetchOne();
```

## 附录：PhalApi对NotORM的优化

如果了解NotORM的使用，自然而然对PhalApi中的数据库操作也就一目了然了。但为了更符合接口类项目的开发，PhalApi对NotORM的底层进行优化和调整。以下改动点包括但不限于：  

 + 将原来返回的结果全部从对象类型改成数组类型，便于数据流通
 + 添加查询多条纪录的接口：```NotORM_Result::fetchAll()```和```NotORM_Result::fetchRows()```
 + 添加支持原生SQL语句查询的接口：```NotORM_Result::queryAll()```和```NotORM_Result::queryRows()```
 + limit 操作的调整，取消原来OFFSET关键字的使用
 + 当数据库操作失败时，抛出PDOException异常
 + 将结果集中以主键作为下标改为以顺序索引作为下标
 + 禁止全表删除，防止误删
 + 调整调试模式
 + 更多优化请见版本更新说明和文档介绍……

这些优化点可以作为课外的兴趣了解。 