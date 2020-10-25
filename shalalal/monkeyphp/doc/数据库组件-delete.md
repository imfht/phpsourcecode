##数据库组件 之 删除数据

删除数据功能都集中在 \Monkey\Database\Delete 类中。你需要获取连接并且通过连接来使用这些功能。

	//获取连接
	$conn = $app->database()->getConnection();
    
    //获取选择数据功能，参数是本次选择数据的主表
    $delete = $conn->delete('tableName');
    

下面捡常用的功能介绍，其它功能见源码。

##指定一个删除条件
######（Delete和Select的查询条件设置方式完全相同）

 1. 常规删除条件

        //删除字段 id 的值等于 5 的数据
        $delete->where('id', 5);

        //删除字段 id 的值为 5 ，9，10 中任一个值的数据
        $delete->where('id', array(5, 9, 10));

        //删除字段 id 的值大于 5 的数据
        $delete->where('id', 5, '>');

 2. 特殊删除条件

		//删除 email 值为空的数据
        $delete->isNull('email');
        
		//删除 email 值为 非 空的数据
        $delete->isNotNull('email');
        
 3. 手动输入删除条件

        //如果你觉得上面的条件设置方法不够爽，还可以这样
        $delete->condition('id > :id', array(':id' => 5));
        
##执行删除

    //辛苦准备那么久了，该执行了
    $stmt = $delete->execute();
		
##获取删除结果信息

    //判断数据是否删除成功
    if ($stmt->isSuccess()) {
        //todo
    }
    
    //查看受影响的数据行数
    $affected = $stmt->affected();
    
    //有可能删除成功了（isSuccess 为真），affected却为0，事实上是操作成功了，只是没有数据受到影响。

    //这个stmt是继承PDOStatement的，所以PDOStatement的功能都有！

##综合例子

    //获取连接
    $conn = $app->database()->getConnection();
    //执行查询
	$affected = $conn->delete('article')
            ->where('a_id', 3)
            ->execute()
            ->affected();