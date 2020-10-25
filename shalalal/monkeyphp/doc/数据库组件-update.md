##数据库组件 之 更新数据

更新数据功能都集中在 \Monkey\Database\Update 类中。你需要获取连接并且通过连接来使用这些功能。

	//获取连接
	$conn = $app->database()->getConnection();
    
    //获取选择数据功能，参数是本次选择数据的主表
    $update = $conn->update('tableName');
    

下面捡常用的功能介绍，其它功能见源码。

##指定字段的更新值

 1. 设置字段的更新值： set 方法

        //设定 name、email、sex 三个字段的值，参数必须为数组，键名是字段名，键值是字段值。
        $update->set(array('name' => 'baocaixiong', 'email' => '大神@126.com', 'sex' => 'unkown'));
        
    设置字段的更新值方法 set 只能使用一次，第二次将使第一次的设置失效。

 2. 设置字段的更新值为表达式： setExpression 方法

		//将点击次数增加 1，参数有 3 个，分别是字段名、表达式、表达式中参数值（参数值为数组）。
        $update->setExpression('times', 'times + :num', array(':num' => 1));

        //也可以这么写，没有参数就省略第三个参数了。
        $update->setExpression('times', 'times + 1');

##指定一个更新条件
######（Update和Select的查询条件设置方式完全相同）

 1. 常规更新条件

        //更新字段 id 的值等于 5 的数据
        $update->where('id', 5);

        //更新字段 id 的值为 5 ，9，10 中任一个值的数据
        $update->where('id', array(5, 9, 10));

        //更新字段 id 的值大于 5 的数据
        $update->where('id', 5, '>');

 2. 特殊更新条件

		//更新 email 值为空的数据
        $update->isNull('email');
        
		//更新 email 值为 非 空的数据
        $update->isNotNull('email');
        
 3. 手动输入更新条件

        //如果你觉得上面的条件设置方法不够爽，还可以这样
        $update->condition('id > :id', array(':id' => 5));
        
##执行更新

    //辛苦准备那么久了，该执行了
    $stmt = $update->execute();
		
##获取更新结果信息

    //判断数据是否更新成功
    if ($stmt->isSuccess()) {
        //todo
    }
    
    //查看受影响的数据行数
    $affected = $stmt->affected();
    
    //有可能更新成功了（isSuccess 为真），affected却为0，主要反映在用户不改变数据就直接提交，如果这里不区别的话就会误以为保存失败，事实上是保存成功了，只是没有数据受到影响。

    //这个stmt是继承PDOStatement的，所以PDOStatement的功能都有！

##综合例子

    //获取连接
    $conn = $app->database()->getConnection();
    //执行查询，其中参数 $data 表示要更新的数据
	$affected = $conn->update('article')
            ->where('a_id', 3)
            ->fields($data)
            ->execute()
            ->affected();