# DataModel数据模型

为了进一步减少数据库操作的代码开发量，避免在开发者在Model子类重复编写代码实现基本的数据库操作。从PhalApi 2.12.0 及以上版本起，我们提供了```PhalApi\Model\DataModel```数据库数据基类。  

如果你是初次使用PhalApi框架，建议在项目开发过程中全部的Model子类都继承于此```PhalApi\Model\DataModel```基类；如果你已经使用PhalApi框架开发有段时间，那么在新的Model或原有的Model子类也可以把原来继承于```PhalApi\Model\NotORMModel```基类调整成```PhalApi\Model\DataModel```，可能的影响是会存在函数名冲突。

下面将介绍DataModel的接口和使用。

> 温馨提示：DataModel需要PhalApi 2.12.0 及以上版本方可支持。如果DataModel提供的接口无法满足数据库的操作需求，你仍然可以在Model子类内部调用NotORM接口。

## 编写你的Model子类

首先，需要创建你的Model子类，每一张数据库的表都应该创建一个对应的Model子类。例如：  
```php
<?php
namespace App\Model;

use PhalApi\Model\DataModel;

class User extends DataModel {
}
```

编写好你的App\Model\User子类后，DataModel会自动映射到user数据库表，如果有配置表前缀，则会自动加上表前缀。

## 数据库常用操作

继承```PhalApi\Model\DataModel```后，你不用写一行代码，就可以直接使用以下CURD常用接口，包括查询、增加、删除、更新、获取列表等操作。

## 简单：4个CURD基本操作

以下4个CURD基本操作是根据id进行单条数据的操作。

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

> 温馨提示：你也可以使用```App\Model\User::model()```静态方法创建实例，等效于```new App\Model\User();```。


## 聚合查询

查询数据是最为常用的操作，你可以取出某个字段的值，也可以取出某条数据纪录，还可以取出多条数据。此外，还能进行聚合运算。

### 查询总数

接口：```PhalApi\Model\DataModel::count($where = NULL, $countBy = '*')```

```php
<?php
use App\Model\User;

// select count(*) from user
$total = User::model()->count();

// select count(id) from user where age > 18
$total = User::model()->count('age > 18', 'id');

// select count(*) from user where name = 'PhalApi' and age = 18
$total = User::model()->count(array('name' => 'PhalApi', 'age' => 18));
```

> 温馨提示：你也可以直接通过new方式来创建Model实例，例如：```$model = new App\Model\User();```，等效于```\App\Model\User::model()```。

### 最小值

接口：```PhalApi\Model\DataModel::min($where, $minBy)```

### 最大值

接口：```PhalApi\Model\DataModel::max($where, $maxBy)```

### 求和

接口：```PhalApi\Model\DataModel::sum($where, $sumBy)```

```php
$model = new \App\Model\User();

// select sum(points) from user where age > 18
$total = $model->sum('age > 18', 'points');
```

## 数据查询

### 获取字段值

接口：```PhalApi\Model\DataModel::getValueBy($field, $value, $selectFiled, $default = FALSE)```

```php
$model = new \App\Model\User();

// select age from user where name = 'PhalApi' limit 0, 1
$age = $model->getValueBy('name', 'PhalApi', 'age');

```

### 获取字段值（多个）

接口：```PhalApi\Model\DataModel::getValueMoreBy($field, $value, $selectFiled, $limit = 0, $isDistinct = FALSE)```

```php
$model = new \App\Model\User();

// select name from user where group_name = '开发者'
$names = $model->getValueMoreBy('group_name', '开发者', 'name');
// 输出例如：array('张三', '李四')

// 取出10个姓名，并去重
// select DISTINCT name from user where group_name = '开发者' limit 0, 10
$names = $model->getValueMoreBy('group_name', '开发者', 'name', 10, true);

```

### 获取一条纪录

接口：```PhalApi\Model\DataModel::getDataBy($field, $value, $select = '*', $default = FALSE)```

```php
$model = new \App\Model\User();

// select * from user where group_name = '开发者' limit 0, 1
$row = $model->getDataBy('group_name', '开发者');

// 也可以使用魔术方法
$row = $model->getDataByGroup_name('开发者');
```


### 获取多条纪录

接口：```PhalApi\Model\DataModel::getDataMoreBy($field, $value, $limit = 0, $select = '*')```

```php
$model = new \App\Model\User();

// select * from user where group_name = '开发者'
$rows = $model->getDataMoreBy('group_name', '开发进');

// 也可以使用魔术方法
$rows = $model->getDataMoreByGroup_name('开发进');
```

### 根据条件，取一条纪录数据

接口：```PhalApi\Model\DataModel::getData($where = NULL, $whereParams = array(), $select = '*', $default = FALSE)```

```php
$model = new \App\Model\User();

$where = 'id = :id';
$whereParams = array(':id' => 1);
$select = 'name,points';

// select name,points from user where id = 1
$user = $model->getData($where, $whereParams, $select);
```

### 根据条件，取列表数组

接口：```PhalApi\Model\DataModel::getList($where = NULL, $whereParams = array(), $select = '*', $order = NULL, $page = 1, $perpage = 100)```

```php
$model = new \App\Model\User();

$where = 'age > :age and points > :points';
$whereParams = array(':age' => 18, 'points' => 100);
$select = '*';
$order = 'id DESC';
// select * from user where age > 18 and points > 100 order by id DESC limit 0, 100
$users = $model->getList($where, $whereParams, $select, $order);
```

## 删除操作

### 删除全部

接口：```PhalApi\Model\DataModel::deleteAll($where)```

```php
$model = new \App\Model\User();

// delete from user where is_banned = 1
$rows = $model->deleteAll('is_banned = 1');
```

### 根据多个ID删除，批量删除

接口：```PhalApi\Model\DataModel::deleteIds($ids)```

```php
$model = new \App\Model\User();

// delete from user where id in (404, 808)
$rows = $model->deleteIds(array(404, 808));
```

## 更新操作

### 更新全部数据

接口：```PhalApi\Model\DataModel::updateAll($where, array $updateData)```

```php
$model = new \App\Model\User();

// update user set points = 0 where is_banned = 1
$model->updateAll('is_banned = 1', array('points' => 0))
```

### 更新计数器

接口：```PhalApi\Model\DataModel::updateCounter($where, array $updateData)```

```php
$model = new \App\Model\User();

// update user set points = points + 1 where is_banned = 1
$model->updateCounter('is_banned = 1', array('points' => 1));

// update user set points = points - 1 where is_banned = 1
$model->updateCounter('is_banned = 1', array('points' => -1));

// update user set points = points + 1, fans_num = fans_num + 1 where is_banned = 1
$model->updateCounter('is_banned = 1', array('points' => 1, 'fans_num' => 1));
```

## 插入操作

### 批量插入

接口：```PhalApi\Model\DataModel::insertMore($datas, $isIgnore = FALSE)```

```php
$model = new \App\Model\User();

$users = array(
     array('name' => '张三'),
     array('name' => '李四'),
);
// insert into user (name) values('张三', '李四')
$rows = $model->insertMore($users);
```

## SQL语句查询与执行

### 执行SQL查询语句

接口：```PhalApi\Model\DataModel::queryAll($sql, $parmas = array())```

```php
$sql = 'select id, name from user where age > :age limit 2';
$params = array(':id' => 18);
$users = \App\Model\User::model()->queryAll($sql, $params);
var_dump($users);

/**
array(
    array('id' => 1, 'name' => '张三'),
    array('id' => 2, 'name' => '李四'),
)
*/
```

### 执行SQL变更语句

接口：```PhalApi\Model\DataModel::executeSql($sql, $params = array())```

```php
$sql = 'update user set age = 18 where name = ?';
$params = array('PhalApi');
$updateNum = \App\Model\User::model()->executeSql($sql, $params);
echo $updateNum; // 输出影响的行数
```

## 第三种获取NotORM的方式

简单回顾一下，前面已经介绍两种获取NotORM的方式，分别是：  

 + 全局获取方式，通过```\PhalApi\DI()->notorm->表名```方式获取，可以用于任何地方。
 + 局部获取方式，通过在继承PhalApi\Model\NotORMModel的子类中使用```$this->getORM()```获取当前Model对应的NotORM，仅限用于Model子类内部。

全局获取方式过于开放自由，局部获取方式过于严格封闭，因此DataModel提供了第三种获取NotORM的方式。如果DataModel自身封装和提供的接口无法满足项目需求时，你可以直接获取NotORM进行连贯式操作。

通过DataModel获取NotORM的第三种方式是，使用：  

 + ```\PhalApi\Model\DataModel::notorm()```静态方法获取

下面几份代码片段分别演示了这三种方式的使用场景。  

### 使用全局获取方式

首先是全局获取方式，例如需要统计全部的用户数量，那么在任何地方，都可以这样编写PHP代码：  
```php
$total = \PhalApi\DI()->notorm->user->count('*');
```
方便之处是不用写多一个Model子类，但缺点是缺少面向对象的封装性，当项目复杂时维护成本高。

### 使用局部获取方式
其次是局部获取方式，还是需要统计用户总数。要先实现你的Model子类，并在内部实现相应的方法。

```php
<?php
namespace App\Model;
use PhalApi\Model\DataModel;

class User extends DataModel {

    public function getTotalNum() {
        // 在Model子类内，局部获取方式，并获取用户总数
        return $this->getORM()
            ->count('*');
    }
}
```
随后在有需要的地方进行调用，通常是在Domain层。  

```php
<?php
namespace App\Domain;
use App\Model\User as UserModel;

class User {

    public function getTotalNum() {
        // 调用上面实现的方法，取用户总数
        $model = new UserModel();
        $total = $model->getTotalNum();
    }
}
```

### 使用DataModel获取方式

最后，自从有了DataModel，你有了第三种选择：使用```PhalApi\Model\DataModel::notorm()```静态方法获取。


通过更少的代码实现相同的效果，并且拥有更灵活的编码能力。下面通过DataModel重新认识一下数据库的操作。

首先，定义你的Model子类。  
```php
<?php
namespace App\Model;
use PhalApi\Model\DataModel;

class User extends DataModel {
}
```

在需要调用的地方编写：  
```php
// 使用NotORM统计大于18岁的用户数量
$total = \App\Model\User::notorm()->where('age > 18')->count('*');
```

那什么时候需要用到这第三种方式呢？答案就是当继承DataModel后需要进行更灵活的数据库操作和查询，而DataModel本身还没有相应的方法接口可以满足时。

三种获取NotORM的方式小结对比如下。  

方式|代码写法|要求|说明|PhalApi版本
---|---|---|---|---
全局获取方式|```\PhalApi\DI()->notorm->表名```|无|任何地方可调用|PhalApi 2.0 及以上
局部获取方式|```$this->getORM()```|须编写Model子类并继承```PhalApi\Model\NotORMModel```|在Model子类内使用，可限制数据库操作都封装在Model子类内|PhalApi 2.0 及以上
DataModel获取方式|```User::notorm()```|须编写Model子类并继承```PhalApi\Model\DataModel```|任何地方可调用，可封闭可开放|PhalApi 2.12.0 以上

## DataModel与NotORMModel的区别

DataModel是比NotORMModel更新推出的数据基类，比NotORMModel功能更强大，并且开发使用更友好。推荐从PhalApi 2.12.0 及以上版本改用DataModel。  

使用DataModel前后的继承关系对比如下：  

![](http://cdn7.okayapi.com/yesyesapi_20200311094556_ff2e117cb312f85e9629ab51a788266c.jpg)

而最大的区别是，DataModel直接提供了对外可用的数据库操作接口，是开放式的；而NotORMModel是封闭式的，很多数据库操作都需要在NotORMModel内部先实现再提供编写好的接口给外部调用。

