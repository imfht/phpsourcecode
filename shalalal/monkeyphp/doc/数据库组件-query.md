##数据库组件 之 通用查询

如果你觉得常用的选择、更新、插入或删除还不够的话不妨直接使用 Connection 类，这个类直接继承自 PDO 类，因此 PDO 有的功能 Connection 都有。

	//获取连接类 Connection 的实例
	$conn = $app->database()->getConnection();

Connection 类最大的特点就是 改写了 query 方法：

	$stmt = $conn->query('SELECT id FROM table_name WHERE id = :id', array(':id'=>1));

可见，query 方法多一个参数，从根本上改善了数据库查询语句的写法。使得查询sql语句可以内嵌查询参数，从而避免了多数的sql语句注入的风险。当然，前提是你要使用第二个参数这个功能。

从源码可以清楚的看到，事实上 query 始终 调用了sql预处理功能，Drupal数据库组件能够防止sql注入的秘密正是源于此处。

##查询调试

在执行查询之后，可以通过 Statement 类提供的 getSQL 方法获得提交到数据库执行的真实语句；

	$realSql = $stmt->getSQL();

还可以通过 Connection 类提供的 getPrepareSQL 方法获取提交到数据库执行之前的预处理语句；

	$prepareSql = $conn->getPrepareSQL();
