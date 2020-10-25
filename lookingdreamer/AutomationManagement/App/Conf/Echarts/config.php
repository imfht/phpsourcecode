<?php
//后台配置文件
return array (
		'TMPL_PARSE_STRING' => array (
				// js cs 在【模板文件】中替换的路径,验证方法在IE能正常打开js、cs脚本
				'__PUBLIC__' => "/App/Tpl/Admin/Public" ,
				'__COMMON__' => "/App/Public" ,
				'__ECHARTS__'  => 'http://localhost/App/Tpl/Admin/Public',
		),
		// 【js之间 】相互调用的模板目录的根目录
		'Echartjs_Index' => '/App/Tpl/Admin/Public',
//各个分区的表的名称         
		'Verify'=>array(
			'一区'=>'yunwei_data_part_one',
			'二区'=>'yunwei_data_part_two',
			'三区'=>'yunwei_data_part_three',
			'四区'=>'yunwei_data_part_four',
			'五区'=>'yunwei_data_part_five',
			'六区'=>'yunwei_data_part_six',
		), 
		'DB_PREFIX' => '', // 数据库表前缀
		'DB_COLLECT' => 'mysql://root:root@localhost:3306/datacollect',
);
?>