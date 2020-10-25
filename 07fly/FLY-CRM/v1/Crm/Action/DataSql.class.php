<?php
/*
 * 数据库升级语句类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class DataSql extends Action{
	private $cacheDir='';//缓存目录
	
	public function sql_event(){
		
/*		$sql="CREATE TABLE fly_tp_package_a SELECT * FROM fly_tp_package WHERE 1=2";
		$this->C($this->cacheDir)->update($sql); 
		exit;
		
		套餐表增加字段sort*/
//		$sql = "SELECT table_name, column_name from information_schema.columns 
//					WHERE table_name = 'fly_tp_package' and column_name LIKE 'sort';";
//		$res = $this->C($this->cacheDir)->countRecords($sql);
//		if(empty($res)){
//			$sql="ALTER TABLE fly_tp_package ADD sort int(4) default 0;";
//			$this->C($this->cacheDir)->update($sql); 		
//		}	
		/* 2015-10-30*/		
//		$s[]="alter table radacct_count_user modify tmp_input bigint(20)  default 0;";
//		$s[]="alter table radacct_count_user modify tmp_output bigint(20) default 0;";
//		$s[]="alter table radacct_count_user modify tmp_amount bigint(20)  default 0;";
//		$s[]="alter table radacct_count_user modify tmp_sessiontime bigint(20)  default 0;";
		$sqllist  =array();
		$sql = "SELECT table_name, column_name from information_schema.columns 
					WHERE table_name = 'fly_sys_user' and column_name LIKE 'identity';";
		$res = $this->C($this->cacheDir)->countRecords($sql);
		if(empty($res)){
			$sqllist[]="ALTER TABLE fly_sys_user ADD identity varchar(50);";
		}
		return $sqllist;
	}
}

?>