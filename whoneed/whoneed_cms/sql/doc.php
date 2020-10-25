<?php
/**
 *	概述
 *
 *	Whoneed管理系统(下面简称：WN)是一套后台管理系统，可以根据业务需求灵活开发;
 *	采用独有的配制式编程，所有的CURD相关的查询，无需编写一句代码，瞬间即可完成!
 */

//============================================== 自定义操作数据配制项
array(
	// 自定义操作链接 {$fid},{$tid}:表Id; {$id}:当前记录的id; 
	// URL:{$fid},{$id} : 需要穿透传递参数，特殊替换url标志 用于子类式的数据管理
	'list' => array(
		'添加子类' => '/admin/auto_form/auto_add/tid/23/type/0/fid/{$id}',
		'内容管理' => 'URL:{$fid},{$id}',
	),

	// 定义CURDV是否显示
	'curd' => array(
		'add'			=> 'no',		// 禁止添加选项
		'update'		=> 'no',		// 禁止编辑选项
        'delete'		=> 'no',		// 禁止删除选项
		'high_search'	=> 'no',		// 禁止高级查询
		'view'			=> 'no_ope',	// no_ope:禁止显示操作列;	no_view:禁止显示查看链接
        'list'          => array('<a id="batchUpdateOnLine" class="add" target="ajaxTodo" href="#" onclick="return doForeach(\'batchUpdateOnLine\', \'/admin/package/update_apk_to_online?ids=\');"><span>批量准备上线</span></a>'),
	),

	// 自定义分类过滤条件
	// value是int,表示整形会做intval()操作
	'type' => array(
		'game_id' => 'int',
	),
	
	// 多级穿透传递参数
	'pass_param' => array(
		'game_id' => 'int',
	),

	// 显示风格
	'show_style' => array(
		
		// 需要按等级格式显示的字段
		'sub_type_data' => array(
			'type_name'
		),

		// 指定字段的风格
		'style' => array(
			'id' => "style='width:80px;'",
			'type_name' => "style='text-align:left; padding-left:10px; width:300px;'"
		),
    ), 

    // 汇总
    'data_count' => array(
        'current' => true,  // 当前页面的数据汇总
        'all'     => true,  // 基于当前条件的，所有数据汇总
    )

    // 查询过滤条件
    'query_condition' => 'AdsProject::funSetCdbTest($arrWhere)'

    // 过滤渠道用户
    'is_user_filter'    => array(
        'user_exist'    => 1,
        'user_ids'      => '1,2,3'
    ), 

    // 高级查询，自定义条件
    
    // 自定义order by 条件，之前是固定的id desc
    'orderby' => "roleid desc",
)

//============================================== 字段数据配制项

// 单行文本框
array(
	'size' => 30
)

// 目前只针对时间控件
array(
    'style' => "style='width:90px;'",   // 形如：2013-08-30 ，这个宽度正好 [需要设置]
    'left_style'  => "style='width:120px;'",                    // 左边一个框 [默认值]
    'right_style' => "style='width:120px; margin-left:5px;'",   // 右边一个框 [默认值]
)

// 带具体时间的设置, 下面的设置已经调整的正好，直接拷贝就可以用了
// ep:2013-08-29 21:01:22
array(
    'style'       => "style='width:140px;'",
    'left_style'  => "style='width:170px;'",
    'right_style' => "style='width:170px; margin-left:5px;'",
    'date_format' => 'yyyy-MM-dd HH:mm:ss',
)

// 多行文本框
array(
	'cols' => 45,	// 列
	'rows' => 10,	// 行
)

// 下拉框
array(
	'function'		=> 'Fun::fun("status")',			// 字段数据来源
	'default_value' => 1,								// 字段默认值
	
	// 这个选项直接指定值数组，function是动态指定，效果一样，有一个就行了
	// function 优先级  > value
	'value'			=> array(
						1 => '列表显示',
						0 => '单页显示'
					),
)

// 时间格式化
array(
	'date_format' => 'yyyy-MM-dd HH:mm:ss'
)

// 行内数据处理
array(
	'list_show_function' => "GameAdserving::funCreateRoleConversionRate(\$rowData)",
)

// 行内链接
array(
'size'=> 50,
'title' => '头图管理',
'url' => '/admin/duoImg/index/pid/',
'mask' => 'true',
'target' => 'dialog',
)
//============================================== 流程数据配制项
// 复选框
array(
        "Flow::flowCheckbox"
)

// 字段处理
array(
        "Flow::addDealField"
)

// 单张图片上传(启用二级图片域名)
array(
        "Flow::uploadImgCdn"
)

// 密码处理
array(
        "Flow::dealPass"
)

// 添加操作用户id
array(
      'Flow::addDealUserId'
)
//============================================== 配制查询表单
array(
	'query_type' => 'like'
)

	/**
	 *	query_type 有如下几个值
	 *
	 *	like		like '%*%'
	 *	left_like	like '*%'
	 *	right_like	like '%*'
	 *	gt			>
	 *	lt			<
	 *	gte			>=
	 *	lte			<=
	 *	between		5 <= * <= 10
	 */

//=============================================== 汇总
AdsProject::funDealRateTest($rowData) //一个参数，固定调用格式

//=========== 查找带回
// 字段 查找带回 数据源设置
array(
    'mc_prefix'     => 'test',
    'mc_back_url'   => '/admin/auto_form/auto_list/tid/63',
    'mc_data_by'    => '63,id,name',    // 数据源，select name from 63 where id = ?
)

// 字段 查找带回 数据流设置
array(
        "Flow::replaceMCNameGetFirst"
)

// 表 查找带回 设置
array(
    'list' => array(
        'MC_SELF' => 'id:id,title',
    ),
)

?>
