# 数据库使用和查询

这一章，主要讲解PhalApi主流的数据库使用方式。


## 常用：数据库操作大全

基本上，全部的常用数据库操作，都可以在下面找到对应的使用说明，以及演示示例。  

假设对于前面的tbl_user表，有以下数据。  

```sql
INSERT INTO `tbl_user` VALUES ('1', 'dogstar', '18', 'oschina', '2015-12-01 09:42:31');
INSERT INTO `tbl_user` VALUES ('2', 'Tom', '21', 'USA', '2015-12-08 09:42:38');
INSERT INTO `tbl_user` VALUES ('3', 'King', '100', 'game', '2015-12-23 09:42:42');
```

下面将结合示例，分别介绍NotORM更为丰富的数据库操作。在开始之前，假定已有：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        $user = $this->getORM();  // 在Model子类内，进行数据库操作前，先获取NotORM实例

        // $user = $this->getORM(1000);  // getORM()的第一个参数是指进行分表的依据，没有时可不传
    }
}
```

## SQL基本语句介绍

 + **SELECT字段选择**  

选择单个字段：    
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
  public function test() {
      // SELECT id FROM `tbl_user`
      return $this->getORM()->select('id')->fetchAll();
  }
}
```

选择多个字段：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT id, name, age FROM `tbl_user`
        return $this->getORM()->select('id, name, age')->fetchAll();
    }
}
```

使用字段别名：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT id, name, MAX(age) AS max_age FROM `tbl_user`
        return $this->getORM()->select('id, name, MAX(age) AS max_age')->fetchAll();
    }
}
```

选择全部表字段：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT * FROM `tbl_user`
        return $this->getORM()->select('*')->fetchAll();

        // 或不指定select字段，默认取全部
        return $this->getORM()->fetchAll();
    }
}
```

选择去重后的字段：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT DISTINCT name FROM `tbl_user`
        return $this->getORM()->select('DISTINCT name')->fetchAll();
    }
}
```

 + **WHERE条件**

单个条件：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // WHERE id = 1（直接表达式）
        return $this->getORM()->where('id = 1')->fetchOne();

        // WHERE id = 1（动态参数）
        return $this->getORM()->where('id', 1)->fetchOne();

        // 或 使用占位符传参
        return $this->getORM()->where('id = ?', 1)->fetchOne();

        // 或 数组形式传参
        return $this->getORM()->where(array('id', 1))->fetchOne();
    }
}
```

多个AND条件：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // WHERE id > 1 AND age > 18
        return $this->getORM()->where('id > ?', 1)->where('age > ?', 18)->fetchAll();

        // 或 用多个 and 连贯操作
        return $this->getORM()->and('id > ?', 1)->and('age > ?', 18)->fetchAll();

        // 或 用含占位符的字符串组合多个条件
        return $this->getORM()->where('id > ? AND age > ?', 1, 18)->fetchAll();

        // 或 用多个元素的数组传参
        return $this->getORM()->where(array('id > ?' => 1, 'age > ?' => 10))->fetchAll();
    }

    public function test2() {
        // 如果只是判断相等，可以直接不用比较符号
        // WHERE name = 'dogstar' AND age = 18
        return $this->getORM()->where(array('name' => 'dogstar', 'age' => 18))->fetchAll();
    }
}
```

多个OR条件：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // WHERE name = 'dogstar' OR age = 18
        return $this->getORM()->or('name', 'dogstar')->or('age', 18)->fetchAll();
    }
}
```

嵌套条件：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // WHERE ((name = ? OR id = ?)) AND (note = ?) -- 'dogstar', '1', 'xxx'

        // 实现方式1：使用AND拼接
        return $this->getORM()->where('(name = ? OR id = ?)', 'dogstar', '1')->and('note = ?', 'xxx')->fetchAll();

        // 实现方式2：使用WHERE，并顺序传递多个参数
        return $this->getORM()->where('(name = ? OR id = ?) AND note = ?', 'dogstar', '1', 'xxx')->fetchAll();

        // 实现方式3：使用WHERE，并使用一个索引数组顺序传递参数
        return $this->getORM()->where('(name = ? OR id = ?) AND note = ?', array('dogstar', '1', 'xxx'))->fetchAll();

        // 实现方式4：使用WHERE，并使用一个关联数组传递参数
        return $this->getORM()->where('(name = :name OR id = :id) AND note = :note', 
            array(':name' => 'dogstar', ':id' => '1', ':note' => 'xxx'))->fetchAll();
    }
}    
```

IN查询：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // 简单的IN查询
        // WHERE id IN (1, 2, 3)
        return $this->getORM()->where('id', array(1, 2, 3))->fetchAll();
    }

    public function test2() {
        // 排除IN
        // WHERE id NOT IN (1, 2, 3)
        return $this->getORM()->where('NOT id', array(1, 2, 3))->fetchAll();
    }

    public function test3() {
        // 多个IN查询
        // WHERE (id, age) IN ((1, 18), (2, 20))
        return $this->getORM()->where('(id, age)', array(array(1, 18), array(2, 20)))->fetchAll();
    }
}
```

模糊匹配查询：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // 像
        // WHERE name LIKE '%dog%'
        return $this->getORM()->where('name LIKE ?', '%dog%')->fetchAll();
    }

    public function test2() {
        // 不像
        // WHERE name NOT LIKE '%dog%'
        return $this->getORM()->where('name NOT LIKE ?', '%dog%')->fetchAll();
    }
}
```
> **温馨提示：**需要模糊匹配时，不可写成：where('name LIKE %?%', 'dog')。  

NULL判断查询：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // WHERE (name IS NULL)
        return $this->getORM()->where('name IS NULL')->fetchAll();
    }
}
```

非NULL判断查询：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // WHERE (name IS NOT NULL)
        return $this->getORM()->where('name IS NOT NULL')->fetchAll();
    }
}
```

 + **ORDER BY排序**  

单个字段升序排序： 
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // ORDER BY age
        return $this->getORM()->order('age')->fetchAll();

        // 或指定排序方式，默认是升序
        return $this->getORM()->order('age ASC')->fetchAll();
    }
}
```

单个字段降序排序： 
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // ORDER BY age DESC
        return $this->getORM()->order('age DESC')->fetchAll();
    }
}
```
  
多个字段排序：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // ORDER BY id, age DESC
        return $this->getORM()->order('id')->order('age DESC')->fetchAll();

        // 或 连起来写
        return $this->getORM()->order('id, age DESC')->fetchAll();
    }
}
```

 + **LIMIT数量限制**

限制数量，如查询前10个：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // LIMIT 10
        return $this->getORM()->limit(10)->fetchAll();
    }
}
```

分页限制，如从第5个位置开始，查询前10个：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // LIMIT 5, 10
        return $this->getORM()->limit(5, 10)->fetchAll();
    }
}
```

另一种分页方式，例如每页10条，分别取第1页、第2页、第3页。代码如下：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // 使用分页，取第1页，即：LIMIT 0, 10
        return $this->getORM()->page(1, 10)->fetchAll();

        // 使用分页，取第2页，即：LIMIT 10, 10
        return $this->getORM()->page(2, 10)->fetchAll();

        // 使用分页，取第3页，即：LIMIT 20, 10
        return $this->getORM()->page(3, 10)->fetchAll();
    }
}
```
> 温馨提示：从PhalApi 2.8.0 及以上版本开始，支持更友好的分页操作，接口为：[page($page, $perpage)](https://github.com/phalapi/notorm/blob/master/src/NotORM/Result.php)，第一个参数表示第几页（从第1页开始），第二个参数表示每页多少条（默认100条）。例如：return $this->getORM()->page(2, 10)->fetchAll();，相当于limt(10, 10)。

 + **GROUP BY和HAVING**

只有GROUP BY，没有HAVING：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // GROUP BY note
        return $this->getORM()->group('note')->fetchAll();
    }
}
```
  
既有GROUP BY，又有HAVING：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // GROUP BY note HAVING age > 10
        return $this->getORM()->group('note', 'age > 10')->fetchAll();
    }
}
```

## CURD之插入操作

插入操作可分为插入单条纪录、多条纪录，或根据条件插入。  


操作|说明|示例|备注|是否PhalApi新增
---|---|---|---|---
insert()|插入数据|```$user->insert($data);```|全局方式需要再调用insert_id()获取插入的ID|否
insert_multi()|批量插入|```$user->insert_multi($rows, $isIgnore = FALSE);```|可批量插入|否，但有优化，```$isIgnore```为TRUE时进行INSERT IGNORE INTO操作
insert_update()|插入/更新|接口签名：```insert_update(array $unique, array $insert, array $update = array()```|不存时插入，存在时更新|否

插入单条纪录数据，注意，必须是保持状态的同一个NotORM表实例，方能获取到新插入的行ID，且表必须设置了自增主键ID。    
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        $data = array('name' => 'PhalApi', 'age' => 1, 'note' => 'framework');

        // INSERT INTO tbl_user (name, age, note) VALUES ('PhalApi', 1, 'framework')
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }
}
```

或者使用Model封装的insert()基本方法
```
// App\Model\User类，不需要额外的实现
$model = new App\Model\User();
$id = $model->insert($data);
var_dump($id); // 返回新增的ID
```

批量插入多条纪录数据：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        $rows = array(
            array('name' => 'A君', 'age' => 12, 'note' => 'AA'),
            array('name' => 'B君', 'age' => 14, 'note' => 'BB'),
            array('name' => 'C君', 'age' => 16, 'note' => 'CC'),
        );

        // INSERT INTO tbl_user (name, age, note) VALUES ('A君', 12, 'AA'), ('B君', 14, 'BB'), ('C君', 16, 'CC')
        // 返回成功插入的条数
        return $this->getORM()->insert_multi($rows);

        // PhalApi 2.2.0 及以上版本才支持
        // 如果希望使用 IGNORE ，可加传第二个参数
        // INSERT IGNORE INTO tbl_user (name, age, note) VALUES ('A君', 12, 'AA'), ('B君', 14, 'BB'), ('C君', 16, 'CC') 
        return $this->getORM()->insert_multi($rows, true);
    }
}
```

插入/更新（组合操作：有则更新，没有则插入）：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        $unique = array('id' => 8);
        $insert = array('id' => 8, 'name' => 'PhalApi', 'age' => 1, 'note' => 'framework');
        $update = array('age' => 2);

        // INSERT INTO tbl_user (id, name, age, note) VALUES (8, 'PhalApi', 1, 'framework') 
        // ON DUPLICATE KEY UPDATE age = 2
        // 返回影响的行数
        return $this->getORM()->insert_update($unique, $insert, $update);
    }
}
```

## CURD之更新操作
  

操作|说明|示例|备注|是否PhalApi新增
---|---|---|---|---
update()|更新数据|```$user->where('id', 1)->update($data);```|更新异常时返回false，数据无变化时返回0，成功更新返回影响的行数|否
updateCounter()|更新单个计数器|接口签名：```updateCounter($column, $number = 1)```，示例：```$user->where('id', 1)->updateCounter('age', 1)```|返回影响的行数|是，PhalApi 2.6.0 版本及以上支持
updateMultiCounters()|更新多个计数器|接口签名：```updateMultiCounters(array $data)```，示例：```$user->where('id', 1)->updateMultiCounters(array('age' => 1))```|返回影响的行数|是，PhalApi 2.6.0 版本及以上支持

根据条件更新数据：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        $data = array('age' => 2);

        // UPDATE tbl_user SET age = 2 WHERE (name = 'PhalApi');

        // 返回更新后的结果（注意区分微妙细节）
        // int(1)，表示 正常影响的行数
        // int(0)，表示 无更新，或者数据没变化
        // boolean(false)，表示 更新异常、失败

        return $this->getORM()->where('name', 'PhalApi')->update($data);
    }
}
```

再重复一下，对于更新后返回的结果。

 + int(1)，表示 正常影响的行数
 + int(0)，表示 无更新，或者数据没变化
 + boolean(false)，表示 更新异常、失败
  
在使用update()进行更新操作时，如果更新的数据和原来的一样，则会返回0（表示影响0行）。这时，会和更新失败（同样影响0行）混淆。但NotORM是一个优秀的类库，它已经提供了优秀的解决文案。我们在使用update()时，只须了解这两者返回结果的微妙区别即可。因为失败异常时，返回false；而相同数据更新会返回0。即：  
 + 1、更新相同的数据时，返回0，严格来说是：int(0)
 + 2、更新失败时，如更新一个不存在的字段，返回false，即：bool(false)
  
用代码表示，就是：  
```php
$model = new \App\Model\User();
$rs = $model->test();

if ($rs >= 1) {
    // 成功
} else if ($rs === 0) {
    // 相同数据，无更新
} else if ($rs === false) {
    // 更新失败
}
```

更新数据，进行加1操作： 
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // UPDATE tbl_user SET age = age + 1 WHERE (name = 'PhalApi')
        // 返回影响的行数
        return $this->getORM()->where('name', 'PhalApi')->update(array('age' => new \NotORM_Literal("age + 1")));
    }
}
```

> 温馨提示：在2.x版本中，当需要使用NotORM_Literal类进行加１操作时，须注意两点：需要先获取NotORM实例再创建NotORM_Literal对象；注意命名空间，即要在最前面加反斜杠。  

上面的数据更新操作更灵活，因为可以在更新其它字段的同时进行数值的更新。但考虑到还需要创建NotORM_Literal对象，增加了认知成本。所以对于简单的计数器更新操作，可以使用updateCounter()接口。

> 注意：updateCounter()接口和updateMultiCounters()接口，需要PhalApi 2.6.0 及以上版本，方可支持。

对比改用updateCounter()接口后的简化版本：
```php
class User extends NotORM {
    public function test() {
        // UPDATE tbl_user SET age = age + 1 WHERE (name = 'PhalApi')
        // 返回影响的行数
        return $this->getORM()->where('name', 'PhalApi')->updateCounter('age');
    }
}
```

须留意到，updateCounter()的第一个参数是字段名称，第二个参数是待更新数值，可以是正数或负数，默认是1，表示加1。返回的结果是影响的行数，而非最新的字段值。下以是更多示例：

```php
// 加1
$this->getORM()->where('name', 'PhalApi')->updateCounter('age', 1);

// 减1
$this->getORM()->where('name', 'PhalApi')->updateCounter('age', -1);
```

与此相似，updateMultiCounters()接口也可用于更新计数器，不同的是此接口可以同时更新多个计数器，且第一个参数是数组。数组下标为字段名，数组元素值为待更新数值。例如：

```php
// age加1，同时points加10
$this->getORM()->where('name', 'PhalApi')->updateMultiCounters(array('age' => 1, 'points' => 10));

// age减1，同时points减10
$this->getORM()->where('name', 'PhalApi')->updateMultiCounters(array('age' => -1, 'points' => -10));
```

## CURD之查询操作

查询操作主要有获取一条纪录、获取多条纪录以及聚合查询等。  

操作|说明|示例|备注|是否PhalApi新增
---|---|---|---|---
fetch()|循环获取每一行|```while($row = $user->fetch()) { ... ... }```||否
fetchOne()|只获取第一行|```$row = $user->where('id', 1)->fetchOne();```|等效于fetchRow()|是
fetchRow()|只获取第一行|```$row = $user->where('id', 1)->fetchRow();```|等效于fetchOne()|是
fetchPairs()|获取键值对|```$row = $user->fetchPairs('id', 'name');```|第二个参数为空时，可取多个值，并且多条纪录；也可以指定单个字段，还可以指定多个字段。|否
fetchAll()|获取全部的行|```$rows = $user->where('id', array(1, 2, 3))->fetchAll();```|等效于fetchRows()|是
fetchRows()|获取全部的行|```$rows = $user->where('id', array(1, 2, 3))->fetchRows();```|等效于fetchAll()|是
queryAll()|复杂查询下获取全部的行，默认下以主键为下标|```$rows = $user->queryAll($sql, $parmas);```|等效于queryRows()|是
queryRows()|复杂查询下获取全部的行，默认下以主键为下标|```$rows = $user->queryRows($sql, $parmas);```|等效于queryAll()|是
count()|查询总数|```$total = $user->count('id');```|第一参数可省略|否
min()|取最小值|```$minId = $user->min('id');```||否
max()|取最大值|```$maxId = $user->max('id');```||否
sum()|计算总和|```$sum = $user->sum('age');```||否

  
循环获取每一行，并且同时获取多个字段：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT id, name FROM tbl_user WHERE (age > 18);
        $orm = $this->getORM()->select('id, name')->where('age > 18');

        while ($row = $orm->fetch()) {
            var_dump($row);
        }
    }
}

// 输出
array(2) {
  ["id"]=>
  string(1) "2"
  ["name"]=>
  string(3) "Tom"
}
array(2) {
  ["id"]=>
  string(1) "3"
  ["name"]=>
  string(4) "King"
}
... ...
```

循环获取每一行，并且只获取单个字段。需要注意的是，指定获取的字段，必须出现在select里，并且返回的不是数组，而是字符串。  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT id, name FROM tbl_user WHERE (age > 18);
        $orm = $this->getORM()->select('id, name')->where('age > 18');

        while ($row = $orm->fetch('name')) { // 指定获取的单个字段 
            var_dump($row); // 此时，输出的是一个字段值，而非一条数组纪录
        }
    }
}


// 输出
string(3) "Tom"
string(4) "King"
... ...

```

注意！以下是错误的用法。还记得前面所学的NotORM状态的保持吗？因为这里每次循环都会新建一个NotORM表实例，所以没有保持前面的查询状态，从而死循环。    
```php
while ($row = $this->getORM()->select('id, name')->where('age > 18')->fetch('name')) {
     var_dump($row);
}
```
  
只获取第一行，并且获取多个字段，等同于fetchRow()操作。  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT id, name FROM tbl_user WHERE (age > 18) LIMIT 1;
        return $this->getORM()->select('id, name')->where('age > 18')->fetchOne();
    }
}

// 返回结果示例
array(2) {
  ["id"]=>
  string(1) "2"
  ["name"]=>
  string(3) "Tom"
}
```

只获取第一行，并且只获取单个字段，等同于fetchRow()操作。   
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT id, name FROM tbl_user WHERE (age > 18) LIMIT 1;
        return $this->getORM()->where('age > 18')->fetchOne('name'); // 只获取单个字段 
    }
}

// 返回结果示例
string(3) "Tom"
```

获取键值对，并且获取多个字段：  
```
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT id, name, age FROM tbl_user LIMIT 2;
        return $this->getORM()->select('name, age')->limit(2)->fetchPairs('id'); //指定以ID为KEY
    }
}

// 返回结果示例
array(2) {
  [1]=> // 下标对应id字段
  array(3) {
    ["id"]=>
    string(1) "1"
    ["name"]=>
    string(7) "dogstar"
    ["age"]=>
    string(2) "18"
  }
  [2]=>
  array(3) {
    ["id"]=>
    string(1) "2"
    ["name"]=>
    string(3) "Tom"
    ["age"]=>
    string(2) "21"
  }
}
```

获取键值对，并且只获取单个字段。注意，这时的值不是数组，而是字符串。  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT id, name FROM tbl_user LIMIT 2
        return $this->getORM()->limit(2)->fetchPairs('id', 'name'); //通过第二个参数，指定VALUE的列
    }
}

// 返回结果示例
array(2) {
  [1]=>
  string(7) "dogstar"
  [2]=>
  string(3) "Tom"
}
```

获取全部的行，相当于fetchRows()操作。  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT * FROM tbl_user
        return $this->getORM()->fetchAll(); // 全部表数据
    }
}
```

## 高级：使用原生SQL语句进行查询

使用原生SQL语句进行查询，并获取全部的行：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT name FROM tbl_user WHERE age > :age LIMIT 1
        $sql = 'SELECT name FROM tbl_user WHERE age > :age LIMIT 1';
        $params = array(':age' => 18);

        return $this->getORM()->queryAll($sql, $params);
    }

    // 除了使用上面的关联数组传递参数，也可以使用索引数组传递参数
    public function test2() {
        // SELECT name FROM tbl_user WHERE age > :age LIMIT 1
        $sql = 'SELECT name FROM tbl_user WHERE age > ? LIMIT 1';
        $params = array(18);

        // 也使用queryRows()别名
        return $this->getORM()->queryRows($sql, $params);
    }
}

// 输出
array(1) {
  [0]=>
  array(1) {
    ["name"]=>
    string(3) "Tom"
  }
}
```

在使用```queryAll()```或者```queryRows()```进行原生SQL操作时，需要特别注意： 

 + 1、需要手动填写完整的表名字，包括分表标识，并且需要通过任意表实例来运行
 + 2、尽量使用参数绑定，而不应直接使用参数来拼接SQL语句，慎防SQL注入攻击  


下面是不好的写法，很有可能会导致SQL注入攻击  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    // 存在SQL注入的写法
    public function test() {
        // 存在SQL注入的写法
        $id = 1;
        $sql = "SELECT * FROM tbl_demo WHERE id = $id";

        // 存在SQL注入的写法
        return $this->getORM()->queryAll($sql);
    }
}
```

对于外部不可信的输入数据，应改用参数传递的方式。  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    // 使用参数绑定方式，避免SQL注入
    public function test() {
        $id = 1;
        $sql = "SELECT * FROM tbl_demo WHERE id = ?";
        
        return $this->getORM()->queryAll($sql, array($id));
    }
}
```

查询总数：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT COUNT(id) FROM tbl_user
        return $this->getORM()->count('id');
    }
}

// 输出
string(3) "3"
```

查询最小值：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT MIN(age) FROM tbl_user
        return $this->getORM()->min('age');
    }
}

// 输出
string(2) "18"
```

查询最大值：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT MAX(age) FROM tbl_user
        return $this->getORM()->max('age');
    }
}

// 输出
string(3) "100"
```

计算总和：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // SELECT SUM(age) FROM tbl_user
        return $this->getORM()->sum('age');
    }
}

// 输出
string(3) "139"
```

## CURD之删除操作
  

操作|说明|示例|备注|是否PhalApi新增
---|---|---|---|---
delete()|删除|```$user->where('id', 1)->delete();```|禁止无where条件的删除操作|否

：  
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // DELETE FROM tbl_user WHERE (id = 404);
        // 按条件进行删除，并返回影响的行数
        return $this->getORM()->where('id', 404)->delete();
    }
}
```

请特别注意，PhalApi禁止全表删除操作。即如果是全表删除，将会被禁止，并抛出异常。如：  

```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // DELETE FROM tbl_user WHERE (id = 404);
        // Exception: sorry, you can not delete the whole table
        // 禁止全表删除！！！
        return $this->getORM()->delete(); 
    }
}
```


## 高级：执行原生sql操作并返回结果

简单总结一下，对于执行原生sql操作的支持，主要有以下三个接口：

 + queryAll/queryRows，主要用于进行SELECT查询，并可以返回查询的数据结果集
 + executeSql，主要用于进行带返回结果的UPDATE、INSERT、DELETE以及数据库变更等操作，但只会返回影响的行数
 + query，最底层的原生操作，不返回任何结果

接下来，简单通过示例说明executeSql()接口的使用。

> 请注意，executeSql()接口需要PhalApi 2.6.0 及以上版本，方可支持。

如果是在Model子类内，可以这样实现：

```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class NotORMTest extends NotORM {
    public function getTableName($id) {
        return 'notormtest';
    }

    public function executeSqlInsert() {
        // 原生插入
        $sql = "INSERT  INTO tbl_notormtest (`content`, `ext_data`) VALUES ('phpunit_e_sql_1', '" . '{\"year\":2019}' . "');";
        return $this->executeSql($sql);
    }

    public function executeSqlUpdate) {
        // 原生更新
        $sql = "UPDATE tbl_notormtest SET `content` = 'phpunit_e_sql_3' WHERE (content = ? OR content = ?);";
        $params = array('phpunit_e_sql_1', 'phpunit_e_sql_2');
        return $this->getORM()->executeSql($sql, $params);
    }

    public function executeSqlDelete() {
        // 原生删除
        $sql = "DELETE FROM tbl_notormtest WHERE (content IN ('phpunit_e_sql_3'));";
        return $this->getORM()->executeSql($sql);
    }
}
```

另一方面，也可以通过全局NotORM实例来调用，并通过单元测试来验证执行的效果是否符合预期。

```php
    public function testExcuteSql()
    {
        // 原生插入
        $sql = "INSERT  INTO tbl_notormtest (`content`, `ext_data`) VALUES ('phpunit_e_sql_1', '" . '{\"year\":2019}' . "');";
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql);

        $this->assertEquals(1, $rs);

        // 原生绑定参数插入
        $sql = "INSERT  INTO tbl_notormtest (`content`, `ext_data`) VALUES (:content, :ext_data);";
        $params = array(':content' => 'phpunit_e_sql_2', ':ext_data' => '{\"year\":2020}');
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql, $params);

        $this->assertEquals(1, $rs);

        // 原生更新
        $sql = "UPDATE tbl_notormtest SET `content` = 'phpunit_e_sql_3' WHERE (content = ? OR content = ?);";
        $params = array('phpunit_e_sql_1', 'phpunit_e_sql_2');
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql, $params);

        $this->assertEquals(2, $rs);

        // 如果是查询呢？只会返回影响的行数，而非结果
        $sql = "SELECT * FROM tbl_notormtest WHERE content IN ('phpunit_e_sql_3')";
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql, $params);

        $this->assertEquals(2, $rs);

        // 原生删除
        $sql = "DELETE FROM tbl_notormtest WHERE (content IN ('phpunit_e_sql_3'));";
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql);

        $this->assertEquals(2, $rs);
    }
```

## 复杂：事务操作、关联查询和其他操作

### 事务操作

事务的操作，主要分为三部分操作。分别是：

 + 开始，开启事务
 + 中间，进行事务数据操作
 + 最后，提交事务，或者回滚操作

事务是针对数据库级别的，下面再分全局方式和局部方式进行说明。

如果使用全局方式获取NotORM实例，那么可以在数据库层面开启事务。以下是事务操作的一个示例。  

```php
    // Step 1: 开启事务
    \PhalApi\DI()->notorm->beginTransaction('db_master');

    // Step 2: 数据库操作
    \PhalApi\DI()->notorm->user->insert(array('name' => 'test1'));
    \PhalApi\DI()->notorm->user->insert(array('name' => 'test2'));

    // Step 3: 提交事务/回滚
    \PhalApi\DI()->notorm->commit('db_master');
    //\PhalApi\DI()->notorm->rollback('db_master');
```

上面通过第一行代码，开启了事务。此时NotORM实例是在./config/di.php文件中注册的默认\PhalApi\DI()->notorm服务，参数db_master是表示数据库标识，对应./config/dbs.php配置中的数据库标识。注意，不是真实的数据库名称。

```php
return array(
    /**
     * DB数据库服务器集群
     */
    'servers' => array(
        'db_master' => array(                         //服务器标记（对应这里的数据库标记）
            'host'      => '127.0.0.1',             //数据库域名
            // 略……
        ),
    ),
);
```

中间的代码，是业务层需要完成的事务操作，例如这里插入了两条数据到user表。
```php
    // Step 2: 数据库操作
    \PhalApi\DI()->notorm->user->insert(array('name' => 'test1'));
    \PhalApi\DI()->notorm->user->insert(array('name' => 'test2'));
```

最后，是提交事务。提交或回滚操作时，参数也是数据库标记，和前面的一样，在这里都是db_master。
```php
    // Step 3: 提交事务/回滚
    \PhalApi\DI()->notorm->commit('db_master');
    //\PhalApi\DI()->notorm->rollback('db_master');
```

另一方面，如果是在Model子类封装的操作，也可以通过当前的局部获取方式获取到NotORM实例后来开启事务。这时，实现方式可以和上面一样。也可以不指定数据库标记来完成，因为已经在./config/dbs.php中配置了数据库表和数据库的映射关系。因此可以这样编写代码：

```php
class User extends NotORM {
    public function doSthImportant() {
        // Step 1: 开启事务
        $this->getORM()->transaction('BEGIN');

        // Step 2: 数据库操作
        $id1 = $this->getORM()->insert(array('name' => 'test1'));
        $id2 = $this->getORM()->insert(array('name' => 'test2'));

        // Step 3: 提交事务/回滚
        if ($id1 >0 && $id > 0) {
            // 提交事务
            $this->getORM()->transaction('COMMIT');
        } else {
            // 回滚事务
            $this->getORM()->transaction('ROLLBACK');
        }
    }
}
```

正如前面所说，此时不需要指定具体的数据库标记。因为每个Model类已经指定了那个数据库。但这里的接口和参数又有所差异。

在Model子类内，可以：

 + 开启事务：$this->getORM()->transaction('BEGIN');
 + 提交事务：$this->getORM()->transaction('COMMIT');
 + 回滚事务：$this->getORM()->transaction('ROLLBACK');


## 关联查询

### 关联查询的推荐写法

从PhalApi 2.12.0 版本起，增加了专门用于关联查询的接口，使用起来更方便，并且更灵活，也是官方推荐的关联查询方式。  

进行关联查询（当是左关联，即LEFT JOIN），主要需要用到的两个接口是：  

 + ```\NotORM_Result::leftJoin($joinTableName, $aliasJoinTableName, $onWhere)```，关联查询，可关联多张表
 + ```\NotORM_Result::alias($aliasTableName)```，主表别表，关联前需要先为当前主表设置别名

先来看一个简单的关联查询例子。  

```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
        public function test() {
            $rs = $this->getORM()
                ->select('A.id AS id, B.title') // 获取字段
                ->alias('A') // 主表别名为A
                // 左关联另一张表another_table（不需要加表前缀）
                // 并起一个别名为B，关联条件是A.id = B.user_id
                ->leftJoin('another_table', 'B', 'A.id = B.user_id')
                ->where('A.id', array(1, 2, 3))
                ->fetchAll();
        }
}
```

最终查询的SQL语句，通过调试模式或日志可以看到，类似如下（假设主表名为user，表前缀设置为tbl_）：  
```sql
SELECT A.id AS id, B.title 
FROM tbl_user AS A 
LEFT JOIN tbl_another_table AS B 
ON A.id = B.user_id 
WHERE (A.id IN (1, 2, 3));
```

再来回顾前面两个接口，首先```leftJoin($joinTableName, $aliasJoinTableName, $onWhere)```接口的三个参数分别是：  
 + ```$joinTableName```：需要关联的表名，不需要加表前缀。框架底层会自动识别并追加表前缀。
 + ```$aliasJoinTableName```：关联表名的别名，可以起一个简单的别名。
 + ```$onWhere```：关联条件，注意使用表别名，并且注意避免SQL注入，暂时不支持动态参数。

如果你需要关联多张表，则继续调用```leftJoin()```接口即可，关联的表数量和次数不限制。其他NotORM连贯接口操作和原来保持不变，但select()时需要使用别名指定表字段，以免字段冲突。  

另外```alias($aliasTableName)```接口用于指定当前主表的别名，在你进行最后的查询操作前，例如fetchOne()/fetchAll()/count()/max()等前，需要先设置主表别名。  

> 温馨提示：PhalApi 2.12.0 及以上版本支持```leftJoin($joinTableName, $aliasJoinTableName, $onWhere)```和```alias($aliasTableName)```关联接口。  

### NotORM自带的关联查询方式

对于关联查询，简单的关联可使用NotORM封装的方式，而复杂的关联，如多个表的关联查询，则可以使用PhalApi封装的接口。  

如果是简单的关联查询，可以使用NotORM支持的写法，这样的好处在于我们使用了一致的开发，并且能让PhalApi框架保持分布式的操作方式。需要注意的是，关联的表仍然需要在同一个数据库。  
  
以下是一个简单的示例。假设我们有这样的数据：  
```sql
INSERT INTO `phalapi_user` VALUES ('1', 'wx_edebc', 'dogstar', '***', '4CHqOhe1', '1431790647', '');
INSERT INTO `phalapi_user_session_0` VALUES ('1', '1', 'ABC', '', '0', '0', '0', null);
``` 
  
那么对应关联查询的代码如下面：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class Session extends NotORM { // 注意，是Session类
    public function test() {
        // SELECT expires_time, user.username, user.nickname FROM tbl_session 
        // LEFT JOIN tbl_user AS user 
        // ON tbl_session.user_id = user.id 
        // WHERE (token = 'ABC') LIMIT 1
        return $this->getORM()
            ->select('expires_time, user.username, user.nickname')
            ->where('token', 'ABC')
            ->fetchRow();
    }
}
```

会得到类似这样的输出：
```php
array(3) {
  ["expires_time"]=>
  string(1) "0"
  ["username"]=>
  string(35) "wx_edebc"
  ["nickname"]=>
  string(10) "dogstar"
}
```
  
这样，我们就可以实现关联查询的操作。按照NotORM官网的说法，则是：  
> If the dot notation is used for a column anywhere in the query ("$table.$column") then NotORM automatically creates left join to the referenced table. Even references across several tables are possible ("$table1.$table2.$column"). Referencing tables can be accessed by colon: $applications->select("COUNT(application_tag:tag_id)").
  
所以```->select('expires_time, user.username, user.nickname')```这一行调用将会NotORM自动产生关联操作，而ON的字段，则是这个字段关联你配置的表结构，外键默认为：表名_id 。

### 更复杂的关联查询方式

如果是复杂的关联查询，则是建议使用原生的SQL语句，但仍然可以保持很好的写法，如这样一个示例：
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class Vote extends NotORM {
    public function test() {
        $sql = 'SELECT t.id, t.team_name, v.vote_num '
          . 'FROM phalapi_team AS t LEFT JOIN phalapi_vote AS v '
          . 'ON t.id = v.team_id '
          . 'ORDER BY v.vote_num DESC';
        return $this->getORM()->queryAll($sql, array());
    }
}
```
如前面所述，这里需要手动填写完整的表名，以及慎防SQL注入攻击。  

## 其他数据库操作

有时，我们还需要进行一些其他的数据库操作，如创建表、删除表、添加表字段等。对于需要进行的数据库操作，而上面所介绍的方法未能满足时，可以使用更底层更通用的接口，即：```\NotORM_Result::query($query, $parameters)```。  

例如，删除一张表。    
```php
<?php
namespace App\Model;
use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function test() {
        // DROP TABLE tbl_user
        return $this->getORM()->query('DROP TABLE tbl_user', array());
    }
}
```
