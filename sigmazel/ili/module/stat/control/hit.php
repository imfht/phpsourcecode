<?php
//版权所有(C) 2014 www.ilinei.com

namespace stat\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/stat/lang.php';

//点击量排行
class hit{
	//默认
	public function index(){
		global $_var, $db;
		
		$rank_list = array();
		
		$temp_query = $db->query("SELECT p.TITLE, p.FILE01, p.PRICE, p.NUM, p.SALEDNUM, p.OURPRICE, p.HITS FROM tbl_product p ORDER BY p.HITS DESC LIMIT 0, 20");
		while(($row = $db->fetch_array($temp_query)) !== false){
			if($row['FILE01']){
				$row['FILE01'] = explode('|', $row['FILE01']);
				$row['FILE01'][0] = format_file_path($row['FILE01'][0], $row['FILE01'][2] + 0);
			}
			
			$rank_list[] = $row; 
		}
		
		include_once view('/module/stat/view/stat.hit');
	}
	
}
?>