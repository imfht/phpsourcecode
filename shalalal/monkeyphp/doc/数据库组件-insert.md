##数据库组件 之 插入数据

插入数据功能都集中在 \Monkey\Database\Insert 类中。你需要获取连接并且通过连接来使用这些功能。

	//获取连接
	$conn = $app->database()->getConnection();
    
    //获取选择数据功能，参数是本次选择数据的主表
    $insert = $conn->insert('tableName');
    

下面捡常用的功能介绍，其它功能见源码。

##指定要插入的字段

    //指定插入 name、email、sex 三个字段，参数必须为数组。
    $insert->fields(array('name', 'email', 'sex'));

    //指定插入 name、email、sex 三个字段，同时添加一行数据。
    $insert->fields(array('name' => 'baocaixiong', 'email' => '大神@126.com', 'sex' => 'unkown'));
        
指定插入字段的方法 fields 只能使用一次，第二次将使第一次的设置失效。

##为部分或全部插入字段指定默认值

    //为 sex 字段指定一个默认值。
    $insert->useDefaults(array('sex' => 1));

##添加插入数据行

 1. 常规添加方法 addRow

        //为插入数据添加一行，参数 $row 为要插入的数据
        $insert->addRow($row);

		//已经指定默认值的字段，在参数 $row 中可以省略，可见此功能必须在指定默认字段值之后

 2. 使用其它查询结果作为插入数据 fromSelect 方法

		//参数 $subQuery 必须是一个 Select 对象
        $insert->fromSelect($subQuery);

##执行插入

    //辛苦准备那么久了，该执行了
    $stmt = $insert->execute();

##获取插入结果信息

    //判断数据是否更新成功
    if ($stmt->isSuccess()) {
        //todo
    }
    
    //查看最后插入行的 id 值
    $affected = $stmt->lastInsertId();
    
    //这个要表扬下自己了，PDOStatement原本是没有这个功能的，我硬加的！
    //然后PDOStatement的功能也都一个不少！

##综合例子

    //获取连接
    $conn = $app->database()->getConnection();
    //执行查询，其中参数 $data 表示要插入的数据（其中既包含插入字段名，又包含字段值）
	$lastInsertId = $conn->insert('article')
            ->fields($data)
            ->execute()
            ->lastInsertId();