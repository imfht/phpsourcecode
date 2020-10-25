<?php
//系统配置
class withdrawModel extends Model {	
	
	protected $_auto = array ( 
		array('add_time','time',1,'function'), // 对addtime字段在更新的时候写入当前时间戳
		array('status','0'),  // 新增的时候把status字段设置为0
	);
	//获取多个的所有信息
	
}
?>