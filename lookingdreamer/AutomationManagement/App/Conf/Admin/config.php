<?php

/*
 * This is NOT a freeware, use is subject to license terms [SEOPHP] (C) 2012-2015 QQ:224505576 SITE: http://seophp.taobao.com/
 */
return array (
		'URL_DISPATCH_ON' => 1,
		'AUTO_BUILD_HTML' => 0,
		'USER_AUTH_ON' => true,
		'USER_AUTH_TYPE' => 1, // 默认认证类型 1 登录认证 2 实时认证
		'RBAC_ROLE_TABLE' => 'role',
		'RBAC_USER_TABLE' => 'role_user',
		'RBAC_ACCESS_TABLE' => 'access',
		'RBAC_NODE_TABLE' => 'node',
		'USER_AUTH_KEY' => 'authId', // 用户认证SESSION标记
		'ADMIN_AUTH_KEY' => 'administrator',
		'USER_AUTH_MODEL' => 'User', // 默认验证数据表模型
		'AUTH_PWD_ENCODER' => 'md5', // 用户认证密码加密方式
		'USER_AUTH_GATEWAY' => '/Admin/Public/login', // 默认认证网关
		'NOT_AUTH_MODULE' => 'Public', // 默认无需认证模块
		'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块
		'NOT_AUTH_ACTION' => '', // 默认无需认证操作
		'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作
		'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
		'GUEST_AUTH_ID' => 0, // 游客的用户ID
		'SHOW_RUN_TIME' => true, // 运行时间显示
		'SHOW_ADV_TIME' => true, // 显示详细的运行时间
		'SHOW_DB_TIMES' => true, // 显示数据库查询和写入次数
		'SHOW_CACHE_TIMES' => true, // 显示缓存操作次数
		'SHOW_USE_MEM' => true, // 显示内存开销
		'LIKE_MATCH_FIELDS' => 'title|remark',
		'TAG_NESTED_LEVEL' => 3,
		'UPLOAD_FILE_RULE' => 'uniqid', // 文件上传命名规则 例如 time uniqid com_create_guid 等 支持自定义函数 仅适用于内置的UploadFile类
		'LOAD_EXT_FILE' => 'add.php',
		// 'DB_PREFIX' => '', // 数据库表前缀
		'DB_COLLECT' => 'mysql://root:root@localhost:3306/datacollect',
		// 'DB_COLLECT' => 'mysql://root:321@127.0.0.1:3306/datacollect',
		// 业务数据统计
		'Verify' => array (
				'一区' => 'yunwei_data_part_one',
				'二区' => 'yunwei_data_part_two',
				'三区' => 'yunwei_data_part_three',
				'四区' => 'yunwei_data_part_four',
				'五区' => 'yunwei_data_part_five',
				'六区' => 'yunwei_data_part_six' 
		),
		// 连接数统计
		'Connect' => array (
				'go2' => 'xitong_connect_go2_part_one',
				'carbiz2' => 'xitong_connect_carbiz2_part_two',
				'carbiz3' => 'xitong_connect_carbiz3_part_three',
				'carbiz4' => 'xitong_connect_carbiz4_part_four',
				'carbiz5' => 'xitong_connect_carbiz5_part_five',
				'carbiz6' => 'xitong_connect_carbiz6_part_six' ,
				'test' => 'xitong_connect_test_part_one',
				'test2' => 'xitong_connect_test2_part_two',
				'test3' => 'xitong_connect_test3_part_three',
				'test4' => 'xitong_connect_test4_part_four',
				'test5' => 'xitong_connect_test5_part_five',
				'test6' => 'xitong_connect_test6_part_six' ,
		),
		'test' =>array('test','rs','foreign'),
		'Go2' => array('test','rs','foreign'),
		// 【js之间 】相互调用的模板目录的根目录
		'Echartjs_Index' => '/Public/Echarts/echarts/Public/',
		'UPLOAD_DIR' => 'upload/',  //一定要有/结尾,前面不能有/
		'DB_INFO' => 'mysql://root:root@localhost:3306/information_schema',
		'RexServer' => 'http://localhost:5000',
		
)
;
?>
