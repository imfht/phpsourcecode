
#自动读写分离的php－orm


## Install
```
composer require man0sions/orm

```
## Run Demo
```
 1:git clone https://git.oschina.net/man0sions/Orm.git
 2:php public/index.php  
```
## useage

### 1:配置数据库
```
$db_conf = [
    'master' => [  //配置主数据库
        'host' => '192.168.10.10',
        'user' => 'mysqluser',
        'passwd' => 'mysqlpasswd',
        'dbname' => 'test',
    ],
    'slave' => [   //配置从数据库,可以多个

        [
            'host' => '192.168.10.11',
            'user' => 'mysqluser',
            'passwd' => 'mysqlpasswd',
            'dbname' => 'test',
        ],
        [
            'host' => '192.168.10.12',
            'user' => 'mysqluser',
            'passwd' => 'mysqlpasswd',
            'dbname' => 'test',
        ]
    ]


];

\LuciferP\Orm\base\Registry::set('db_conf', $db_conf);

```

### 2: model 创建模型

```
例如:创建一个User.php

use LuciferP\Orm\base\Model;

class Users extends Model
{
    protected $table = 'users'; //修改此处mysql表名称

    
}

```

### 3: create (插入数据)
```
/**
 * create
 */

$user = new \LuciferP\Orm\models\Users();
$user->name = 'zhangsan';
$user->password = password_hash('passwd',PASSWORD_DEFAULT,['cost'=>10]);

if($user->create())
{
    var_dump($user->getAttributes());
}
else
{
    var_dump($user->getErrors()); //sql操作失败用getErrors()方法获取错误信息
}


```
### 4: find (查询数据)

```

/**
 * find
 */

$user = \LuciferP\Orm\models\Users::model()
    ->fields(['*'])
    ->where(['id' => 2])
    ->find();

var_dump($user->getAttributes());  //使用getAttributes 方法获取数据数组



```
### 5:findall (查询集合)
```

/**
 * find all
 * findall 方法返回的是一个数组对象,数组中的每一个对象都可以进行update,delete,操作
 */

$users = \LuciferP\Orm\models\Users::model()
    ->fields(['*'])
    ->where(['name' => 'zhangsan'])
    ->limit(5)
    ->order(['id'=>'desc'])
    ->findAll();

foreach($users as $item)
{
    var_dump($item->getAttributes());
}

```
### 5: update (更新数据)

```
/**
 * update
 */
$user->name = 'lisi'.microtime();
if($user->update())
{
    var_dump($user->getAttributes());
}
else
{
    var_dump($user->getErrors());
}

```


### 6: delete (删除数据)

```
/**
 * delete
 */

if(!$user->delete())
{
    var_dump($user->getErrors());

}

```

### 7: save (create/update)

```
$user = \LuciferP\Orm\models\Users::model()
    ->fields(['*'])
    ->where(['id' => 2])
    ->find();
if (!$user->$user->getAttributes()) {
    $user = new User();
}
$user->name = "hello";
if ($user->save()) {
    var_dump($user->getAttributes());
} else {
    var_dump($user->getErrors());
}

```


### 8: count  (计算总数)

```
$count = Users::model()->where(['name'=>'zhangsan'])->count();



```
