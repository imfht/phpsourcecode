<?php
//版权所有(C) 2014 www.ilinei.com

namespace stat\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/stat/lang.php';

//积分排行
class score{
	//默认
	public function index(){
		global $_var, $db;
		
		$score_list = array();
		
		$temp_query = $db->query("SELECT * FROM tbl_user WHERE ISMANAGER = 0 ORDER BY SCORE DESC LIMIT 0, 20");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$score_list[] = $row; 
		}
		
		include_once view('/module/stat/view/score');
	}
	
}
?>