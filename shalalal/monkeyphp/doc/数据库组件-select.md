##数据库组件 之 选择数据

选择数据功能都集中在 \Monkey\Database\Select 类中。你需要获取连接并且通过连接来使用这些功能。

	//获取连接
	$conn = $app->database()->getConnection();
    
    //获取选择数据功能，参数是本次选择数据的主表
    $select = $conn->select('tableName');
    
    //获取选择数据功能，并且给主表指定一个别名
    $select = $conn->select('tableName', 'alias');

下面捡常用的功能介绍，其它功能见源码。

##添加结果集中需要的字段

 1. 添加主表字段： fields 方法

        //添加主表中的所有字段，这句一般，因为不用就相当于默认选择主表中的所有字段了，当有从表时，这句就必要了
        $select->fields();
        
        //添加主表中的 1 个字段
        $select->fields('f1');
        
        //添加主表中的 3 个字段
        $select->fields('f1', 'f2', 'f3');
        
        //添加主表中的 1 个字段，并且将字段'f1'设置为别名 'aliasF1'
        $select->fields(array('f1'=>'aliasF1'));
        
        //添加主表中的 3 个字段，并且将字段 'f2' 的别名设置为 'aliasF2'
        $select->fields(array('f1', 'aF2'=>'aliasF2', 'f3'));
        
 2. 添加指定表的字段： addFields 方法

        //添加别名为tableAlias的表中的 所有个字段
        $select->fields('tableAlias');
        
        //添加别名为tableAlias的表中的 1 个字段
        $select->fields('tableAlias', 'f1');
        
        //添加别名为tableAlias的表中的 3 个字段，并且将字段 'f2' 的别名设置为 'aliasF2'
        $select->fields('tableAlias', array('f1', 'aF2'=>'aliasF2', 'f3'));
		
    这个方法和添加主表字段方法差不多，区别有二，一是两个参数，第一个参数必须是表别名，第二个参数才是选择的字段；二是第二个参数有多个字段或有字段别名时必须为数组。

 3. 添加一个表达式到结果集中

		//给统计查询添加一个COUNT(*)表达式，并在结果集中显示为 mk_count_value ，表达式中还可以使用 {:表名:} 或直接使用 表别名
        $select->addFieldByExpression('mk_count_value', 'COUNT(*)');

##指定一个查询条件

 1. 常规查询条件

        //选择字段 id 的值等于 5 的数据
        $select->where('id', 5);

        //选择字段 id 的值为 5 ，9，10 中任一个值的数据
        $select->where('id', array(5, 9, 10));

        //选择字段 id 的值大于 5 的数据
        $select->where('id', 5, '>');

 2. 特殊查询条件

		//选择 email 值为空的数据
        $select->isNull('email');
        
		//选择 email 值为 非 空的数据
        $select->isNotNull('email');
        
 3. 手动输入查询条件

        //如果你觉得上面的条件设置方法不够爽，还可以这样
        $select->condition('id>:id', array(':id' => 5));
        
##对结果排序、分组和筛选

 1. 排序

		//对id字段做升序排列
        $select->orderBy('id');
        
		//对id字段做降序排列，第二个参数只要不是 asc 就是降序哈哈
        $select->orderBy('id', 'any');
		
 2. 分组

		//对id字段分组
        $select->groupBy('id');

 3. 筛选

		//筛选出 id 字段为5的
        $select->having('id', 5);
        
		//筛选出email字段为空的
        $select->having('email', NULL, 'IS NULL');
        
		//筛选出email字段为空的
        $select->havingIsNull('email');
        
        //类似还有havingIsNotNull 和 havingCondition方法 与where的用法差不多
        
 4. 范围

		//筛选出最多 10 结果，从 第 0 个结果算起
        $select->range(10, 0); //和mssql一致
        $select->range(10); //和mssql一致
        
		//筛选出最多 10 结果，从 第 0 个结果算起
        $select->limit(0, 10); //和mysql一致
        
		//两个方法差不多，看个人习惯。我喜欢前者，比如下面两个意思相同，但明显range更好理解：
        $select->range(1);
        $select->limit(0);
        
##执行查询

    //辛苦准备那么久了，该执行了
    $stmt = $select->execute();
		
##获取查询结果信息

    //获取一条数据，默认为关联数据
    $row = $stmt->fetch();

    //获取所有数据，默认为关联数据
    $rows = $stmt->fetchAll();

    //获取所有数据行数
    $counts = $stmt->rowCount();

    //判断数据是否获取成功
    if ($stmt->isSuccess()) {
        //todo
    }

    //这个stmt是继承PDOStatement的，所以PDOStatement的功能都有！

##统计查询
当你设置好了查询条件后，在真正查询前还可以预先知道满足条件的数据一共有多少行，如下

	//指定统计别名
    $newSelect = $select->getCountQuery('myCountAlias');
    $row = $newSelect->execute()->fetch();
    $count = $row['myCountAlias'];
    
    //习惯了可以像下面这样
    $row = $select->getCountQuery('myCountAlias')->execute()->fetch();
    $count = $row['myCountAlias'];
    
    //也可以使用默认的统计别名 mk_count_value
    $row = $select->getCountQuery()->execute()->fetch();
    $count = $row['mk_count_value'];
    
需要注意的是：
 1. getCountQuery返回的是一个新的 $newSelect 选择对象，原来的选择对象并没有发生变化，方便继续使用原来的查询获取真正具体的数据行。
 2. getCountQuery返回的是新的选择对象，所以必须在准备好了查询之后才能使用。
 3. 所有以get开头的方法都有先设置后获取的要求。

##联合查询
联合方法有多种：join、innerJoin、leftJoin、rightJoin，其中join和innerJoin等价的

	//有 3 个参数，分别是：联合的表名、它的别名、和别的表联合的条件
    $select->join('other_table2', 'aliasT2', 'aliasT2.t2_id = aliasT1.t2_id');

##综合例子

    //获取连接
    $conn = $app->database()->getConnection();
    //执行查询，其中参数 $page 表示页码， $pageLength 表示每页数据条数
	$data = $conn->select('article', 'a')
            ->fields('a_id', 'title', 'a_name', 'top_sort', 'add_time', 'click')
            ->join('article_category', 'ac', 'a.ac_id = ac.ac_id')
            ->addFields('ac', array('caption' => 'ac_caption'))
            ->orderBy('a.a_id', 'DESC')
            ->range($pageLength, $page*$pageLength)
            ->execute()
            ->fetchAll();