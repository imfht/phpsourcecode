EasyDB是一个基于PDO的数据库操作类，它使用了PDO的精髓--预编译--的思想，从而在根源上杜绝SQL注入的危险。
现在网上的大多数据库封装类，甚至一些知名的框架都仅仅只是包装一下数据库的操作，内部还是使用拼装字符串的方式执行语句！
这也太LOW了！而且为了防止SQL注入，还为此增加了超多的格式验证和数据过滤，验证影响代码维护和执行的效率。
而EasyDB是优雅的，它继承自PDO,没有破坏PDO的任何方法，只是在此基础上增加一些易于使用操作方法和简单的链式操作，
它的的设计初衷就是完善和简化MySQL等数据库的相关操作。引入它，你仍可以直接像使用PDO那样使用它。

##使用方法##
在你的项目中引入EasyDB.php文件之后，你就可以像这样使用：

$db = new EasyDB();
$sql = 'select * from table where id = ?';
$result = $db->queryOne($sql, array(2));

好的，你现在就已经获取到你想要的结果了！同样，您也可以这样写：

$db = new EasyDB();
$sql = 'select * from table where id = :id';
$array = array(
      ':id' => 2
);
$result = $db->queryOne($sql, $array);

我强烈建议你不要在直接用变量拼装SQL语句，这是危险和不明智的，因为已经有PDO预编译这么好的机制，为什么不用它呢？就像上面的例子，用它是不是很简单。

除了常用的增删改查方法，EasyDB还封装了一些简单的链式操作，注意：这里只是一些简单的链式操作，对于复杂的语句还是执行SQL语句来的痛快。

$db = new EasyDB();
$db->table_select('table')->field('name')->where('id=?')->go(array(2));

是的。链式操作就是这么简单。


###使用手册##
提示：一般在SQL语句中使用了占位符，那么紧接着就应该传入一个绑定数组，如：
例1：
$sql = 'select * from my';
$result = $db->queryOne($sql);//因为SQL语句中没有占位符，所以只需要传入$sql参数即可

例2：
$sql = 'select * from my where id=?';
$result = $db->queryOne($sql, array(4));//这里的第二个参数array(4)就是将数组中的值4传给之前占位的?

例3：
$sql = 'select * from my where name=? and age=?';
$result = $db->queryOne($sql, array('blue', 18));//这里的第二个参数array('blue', 18)的值分别对应SQL语句中的两个？

例4：
$sql = 'select * from my where name=:name and age=:age';
$data = array(
	':name' => 'blue',
	':age'  => '18
);
$result = $db->queryOne($sql, $data);//这是另外一种占位方式


如果你是手写SQL党，对于查询（select）可以这样使用：
例1：
$sql = 'select * from my where id=?';
$result = $db->queryOne($sql, array(4));

例2：
$sql = 'select * from my';
$result = $db->queryAll($sql);

其中queryOne()返回一维数组（即一条记录），queryAll()返回二维数组（即多条记录）


对于/insert/delete/update,可以这样使用：
例1：
$sql = 'insert into table set name=? , age=?';
$db->querySql($sql, array('blue', 18));

例2：
$sql = 'update table where id=?';
$db->querySql($sql, array(4));


一些复杂的语句，可以直接这样使用：
$sql = '....';//复杂的语句
$stmt = $db->queryObj($sql);//这里会返回一个SQL语句对象
然后就可以执行一些操作，如 $stmt->rowCount();


再次提醒：EasyDB是直接继承于PDO的，所以就算EasyDB不能满足你的使用，直接用PDO的方法是完全可以的。


如果你喜欢简化的方式使用SQL，那就可以这样：
对于select：
$data = array(
	':name' => 'blue',
	':age'  => '18
);
$db->table_select('table')->where('name=:name and age=:age')->go($data);//对于查询，使用链式的方式


对于insert:
$data = array(
	'name' => 'blue',
	'age'  => '18
);
$db->insert('table', $data);//$data中的键为数据库字段名，值为对应的数值


对于delete:
$where = array(       
	':name' => 'blue',
	':age'  => '18
);
$db->delete('table', $where);//$where之间的关系为and


对于update:
$where = array(
	'id' => 4
);
$data = array(
	'name' => 'blue',
	'age'  => '18
);
$db->update('table', $data, $where);//$where之间的关系为and


如果insert/update/delete的条件比较复杂，可以使用链式方式，如：
$data = array(
	':name' => 'blue',
	':age'  => '18,
	':source' => 100
);
$db->table_update('table')->where('name=:name or age=:age')->setdata('source=:source')->go($data);


另外EasyDB还独立了一个方法，count()，用于计算条目总数，如：
$db->count('table');//table表的总条目
$db->count('table', 'age=?' , array(18));//符合条件的总条目




好了，具体的一些方法大家可以直接看源码，我在其中都有注释，如果有什么问题可以直接微博@deng-dev (http://weibo.com/fensiyun)

