<?php
/*
 *
 * admin.MemberTypeDist 会员分组提成设置分类管理   
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
class MemberTypeDist extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		//_instance('Action/sysmanage/Auth');
	}	
	//查询
	public function member_type_dist($member_type_id){
		$sql 	="select * from fly_member_type_dist where member_type_id='$member_type_id' order by layers asc";
		$list	=$this->C($this->cacheDir)->findAll($sql);	
		return $list;
	}
	//传入ID返回名字
	public function member_type_dist_get_name($member_type_id){
		if(empty($id)) $id=0;
		$sql ="select * from fly_member_type_dist where member_type_id='$member_type_id'  order by layers asc";
		$list=$this->C($this->cacheDir)->findAll($sql);
		$str ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "[".$row['layers']."层=".$row['rate']."]&nbsp;&nbsp;";
			}
		}
		return $str;
	}
	
	//增加
	public function member_type_dist_add($member_type_id,$data=array()){
		$layers	  = $data["layers"];
		$rate	  = $data["rate"];
		//先删除 
		$sql="delete from fly_member_type_dist where member_type_id='$member_type_id'";
		$this->C($this->cacheDir)->update($sql);	
		//后增加
		for ($irow=0 ; $irow<count($layers); $irow++){
			$sql= "insert into fly_member_type_dist(layers,rate,member_type_id) 
								values('$layers[$irow]','$rate[$irow]','$member_type_id');";
			$this->C($this->cacheDir)->update($sql);	
		}
	}
}//
?>