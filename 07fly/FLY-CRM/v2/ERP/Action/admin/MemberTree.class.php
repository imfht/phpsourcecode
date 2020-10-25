<?php
/*
 *
 * admin.MemberTree 会员推荐关系管理
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
class MemberTree extends Action{	

	private $cacheDir='';//缓存目录
	public function __construct() {
		/*$this->auth=_instance('Action/sysmanage/Auth');*/
	}
	
	//插入一个节点
	public function member_tree_add($parent_id,$member_id){
		$sql="INSERT INTO fly_member_tree(ancestor,descendant)    
	 					SELECT t.ancestor,$member_id FROM fly_member_tree AS t  WHERE t.descendant = '$parent_id'
						UNION ALL
    				  SELECT $member_id,$member_id
						";
		$rtn=$this->C($this->cacheDir)->update($sql);
		if($rtn>=0){
			return true;	
		}else{
			return false;
		}
	}
	
	//rutrun array();返回子节点
	public function get_member_son($id){
		$rtnArr=array();
		$sql ="select id from fly_member where parent_id='$id' order by id desc";
		$list=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtnArr[$key]=$row["id"];
			}
		}
		return $rtnArr;
	}		
	//rutrun array();返回祖节点
	public function get_member_ancestor($id,$level=null){
		$rtnArr=array();
		$limit ="";
		if($level) $limit=" limit 1,$level";
		//$sql ="select ancestor from fly_member_tree where descendant='$id' and ancestor<>'$id' order by ancestor desc $limit";//不包括本身
		$sql ="select ancestor from fly_member_tree where descendant='$id' order by ancestor desc $limit";//不包括本身
		$list=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtnArr[$key]=$row["ancestor"];
			}
		}
		return $rtnArr;
	}
	//rutrun array();返回孙节点
	public function get_member_descendant($id,$level=null){
		$rtnArr=array();
		$limit ="";
		if($level) $limit=" limit 0,$level";
		$sql ="select descendant from fly_member_tree where ancestor='$id' order by descendant asc $limit";
		$list=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$key]=$row["descendant"];
			}
		}
		return $rtnArr;
	}
}//
?>