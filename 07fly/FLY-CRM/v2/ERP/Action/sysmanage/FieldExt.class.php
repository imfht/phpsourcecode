<?php
/*
 *
 * sysmanage.FieldExt  表字段操作类   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */	
class FieldExt extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		 _instance('Action/sysmanage/Auth');
	}
	
	//检查表字段是否存在
	// return true/false
	public function check_field( $table, $colField){
		$fields = $this->get_field( $table );
		if (in_array( $colField, $fields ) ) {
			return true;
		}else{
			return false;
		}
	}
	
	//查询表的所有字段
	public function get_field( $table){
		$database = _instance( 'Database', '', 1 );
		$result  = $database->getFields( $table ); //执行更新的SQL语句
		return $result;
	}
	
	//增加字段
	public function add_field($table,$fied,$type,$maxlength,$default=NULL,$desc=NULL){
		$field_exits=$this->check_field( $table, $fied);
		if(!$field_exits){
			if($type=='varchar'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` varchar($maxlength) COMMENT '$desc'";
			}elseif($type=='textarea'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` varchar($maxlength) COMMENT '$desc'";
			}elseif($type=='htmltext'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` text COMMENT '$desc'";
			}elseif($type=='int'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` int(8) default 0.00 COMMENT '$desc'";
			}elseif($type=='float'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` decimal(10,2) default 0.00 COMMENT '$desc'";
			}elseif($type=='datetime'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` datetime COMMENT '$desc'";
			}elseif($type=='date'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` date COMMENT '$desc'";
			}else{
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` varchar($maxlength) COMMENT '$desc'";	
			}
			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>0){
				return array('statusCode'=>200,'message'=>'添加成功');
			}else{
				return array('statusCode'=>300,'message'=>'添加出错');
			}
		}else{
			return array('statusCode'=>300,'message'=>'字段已经存');
		}
	}
	//增加字段
	public function modify_field($table,$fied,$type,$maxlength,$desc=null){
		$field_exits=$this->check_field( $table, $fied);
		if($field_exits){
			if($type=='varchar'){
				$sql="ALTER TABLE `$table` MODIFY `$fied` varchar($maxlength) COMMENT '$desc'";
			}elseif($type=='textarea'){
				$sql="ALTER TABLE `$table` MODIFY `$fied` varchar($maxlength) COMMENT '$desc'";
			}elseif($type=='htmltext'){
				$sql="ALTER TABLE `$table` MODIFY `$fied` text COMMENT '$desc'";
			}elseif($type=='int'){
				$sql="ALTER TABLE `$table` MODIFY `$fied` int(8) default 0.00 COMMENT '$desc'";
			}elseif($type=='float'){
				$sql="ALTER TABLE `$table` MODIFY `$fied` decimal(10,2) default 0.00 COMMENT '$desc'";
			}elseif($type=='datetime'){
				$sql="ALTER TABLE `$table` MODIFY `$fied` datetime COMMENT '$desc'";
			}elseif($type=='date'){
				$sql="ALTER TABLE `$table` MODIFY `$fied` date COMMENT '$desc'";
			}else{
				$sql="ALTER TABLE `$table` MODIFY `$fied` varchar($maxlength) COMMENT '$desc'";	
			}
			$rtn=$this->C($this->cacheDir)->update($sql);
			return array('statusCode'=>200,'message'=>'修改成功');

		}else{
			if($type=='varchar'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` varchar($maxlength) COMMENT '$desc'";
			}elseif($type=='textarea'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` varchar($maxlength) COMMENT '$desc'";
			}elseif($type=='htmltext'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` text COMMENT '$desc'";
			}elseif($type=='int'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` int(8) default 0.00 COMMENT '$desc'";
			}elseif($type=='float'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` decimal(10,2) default 0.00 COMMENT '$desc'";
			}elseif($type=='datetime'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` datetime COMMENT '$desc'";
			}elseif($type=='date'){
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` date COMMENT '$desc'";
			}else{
				$sql="ALTER TABLE `$table` ADD COLUMN `$fied` varchar($maxlength) COMMENT '$desc'";	
			}
			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>0){
				return array('statusCode'=>200,'message'=>'添加成功');
			}else{
				return array('statusCode'=>300,'message'=>'添加出错');
			}
		}	
	}
	//增加字段
	public function del_field($table,$fied){
		$sql="ALTER TABLE `$table` DROP `$fied`";
		$rtn=$this->C($this->cacheDir)->update($sql);
		return true;
	}
	
	
	
}//end class
?>