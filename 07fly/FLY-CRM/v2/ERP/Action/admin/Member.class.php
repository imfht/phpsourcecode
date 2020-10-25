<?php
/*
 *
 * admin.Member  会员管理   
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
class Member extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
		$this->member_type=_instance('Action/admin/MemberType');
	}	
	public function member(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   = $this->_REQUEST("name");
		$tel	   = $this->_REQUEST("tel");
		$linkman   = $this->_REQUEST("linkman");
		$fax   	   = $this->_REQUEST("fax");
		$email     = $this->_REQUEST("email");
		$address   = $this->_REQUEST("address");	
		$sdt1   	   = $this->_REQUEST("sdt1");
		$edt1   	   = $this->_REQUEST("edt1");
		$where_str = "member_id != 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		if( !empty($name) ){
			$where_str .=" and name like '%$name%'";
		}
		if( !empty($tel) ){
			$where_str .=" and tel like '%$tel%'";
		}	
		if( !empty($linkman) ){
			$where_str .=" and linkman like '%$linkman%'";
		}	
		if( !empty($ecotype) ){
			$where_str .=" and ecotype ='$ecotype'";
		}	
		if( !empty($trade) ){
			$where_str .=" and trade ='$trade'";
		}	
		if( !empty($fax) ){
			$where_str .=" and fax like '%$fax%'";
		}	
		if( !empty($email) ){
			$where_str .=" and email like '%$email%'";
		}	
		if( !empty($address) ){
			$where_str .=" and address like '%$address%'";
		}	
		if( !empty($sdt1) ){
			$where_str .=" and adt >= '$sdt1'";
		}			
		if( !empty($edt1) ){
			$where_str .=" and adt < '$edt1'";
		}		
		
		//排序生成
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_adt' ){
			$order_by .=" adt $orderDirection";
		}else{
			$order_by .=" member_id desc";
		}
		
		//**************************************************************************
		$countSql   = "select * from fly_member where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		
		//**************************************************************************统计总积分，
		$sumSql	="select sum(balance) as all_balance, sum(integral) as all_integral from fly_member where $where_str";
		$sumOne	= $this->C($this->cacheDir)->findOne($sumSql);
		
		$sql		 = "select * from fly_member  where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['parent_name']=$this->member_get_name($row['parent_id']);
			$list[$key]['type_name']=$this->member_type->member_type_get_name($row['member_type_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
		
	}
	public function member_json(){
		$assArr  = $this->member();
		echo json_encode($assArr);
	}	
	public function member_show(){
			$assArr  = $this->member();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/member_show.html');	
	}

	public function member_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('admin/member_add.html');	
		}else{
			$parent_id = $this->_REQUEST("parent_id");	
			$into_data=array(
				'account'=>$this->_REQUEST("account"),
				'password'=>$this->_REQUEST("password"),
				'password_pay'=>$this->_REQUEST("password"),
				'name'=>$this->_REQUEST("name"),
				'qicq'=>$this->_REQUEST("qicq"),
				'email'=>$this->_REQUEST("email"),
				'mobile'=>$this->_REQUEST("mobile"),
				'integral'=>$this->_REQUEST("integral"),
				'balance'=>$this->_REQUEST("balance"),
				'parent_id'=>$this->_REQUEST("parent_id"),
				'intro'=>$this->_REQUEST("intro"),
				'adt'=>NOWTIME,
			);
			$this->C($this->cacheDir)->begintrans();
			$rtn=$this->C($this->cacheDir)->insert('fly_member',$into_data);
			if($rtn>0){
				$this->L('admin/MemberTree')->member_tree_add($parent_id,$rtn);
			}
			//事件提交
			$this->C($this->cacheDir)->commit();
			$this->location("操作成功","/admin/Member/member_show/");	
		}
	}		
	
	
	public function member_modify(){
		$member_id = $this->_REQUEST("member_id");
		if(empty($_POST)){
			$sql 		= "select * from fly_member where member_id='$member_id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$one['parent_name']=$this->member_get_name($one['parent_id']);
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one));
			$smarty->display('admin/member_modify.html');	
		}else{//更新保存数据
			$upt_data=array(
				'account'=>$this->_REQUEST("account"),
				'password'=>$this->_REQUEST("password"),
				'password_pay'=>$this->_REQUEST("password"),
				'name'=>$this->_REQUEST("name"),
				'qicq'=>$this->_REQUEST("qicq"),
				'email'=>$this->_REQUEST("email"),
				'mobile'=>$this->_REQUEST("mobile"),
				'integral'=>$this->_REQUEST("integral"),
				'balance'=>$this->_REQUEST("balance"),
				'intro'=>$this->_REQUEST("intro"),
				'adt'=>NOWTIME,
			);
			$this->C($this->cacheDir)->modify('fly_member',$upt_data,"member_id='$member_id'");	
			$this->location("操作成功","/admin/Member/member_show/");		
		}
	}
	
		
	public function member_del(){
		$member_id = $this->_REQUEST("member_id");
		$sql  = "delete from fly_member where member_id in($member_id)";
		$this->C($this->cacheDir)->update($sql);	
		$rtnArr=array('rtnstatus'=>'success','msg'=>'删除成功');
		echo json_encode($rtnArr);
	}	
	
	public function member_list(){
		$sql	="select member_id,account,name from fly_member";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}		

	//传入ID返回名字
	public function member_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select member_id,account as name from fly_member where member_id in ($id) order by member_id desc";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $key=>$row){
				$str .= " ".$row["name"]."";
			}
		}
		return $str;
	}
	
	//返回用户的下组用户
	public function member_get_son_list($id){
		$sql  ="select * from fly_member where parent_id='$id' order by id desc";
		$list  =$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}

}//
?>