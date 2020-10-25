<?php
/*
 * 该类用于提供基础性的数据库服务
 * */
 
 class Basic{
	 protected $dbc;//数据库连接对象
	 
	function __construct($dbc){
		$this->dbc = $dbc;
		
		define("USER_ALREADY_EXISTS", -1);//用户名已存在
		define("INVALID_USER", -2);//无效的用户编号
		define("NO_RECORDS", -3);//没有记录
		define("QUERY_FAILED", -4);//查询失败
		define("LOGIN_FAILED", -5);//登录失败
		define("SEP_I", ",");//一级分隔符
		define("SEP_II", ":");//二级分隔符
	} 
}
?>
