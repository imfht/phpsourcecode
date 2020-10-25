<?php
/*
 *
 * admin.Shop  店铺管理   
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
class Shop extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
		$this->type=_instance('Action/admin/ShopType');
		$this->member=_instance('Action/admin/Member');
		
	}	
	public function shop(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name = $this->_REQUEST("name");
		$tel	   = $this->_REQUEST("tel");
		$status   = $this->_REQUEST("status");
		$fax   	   = $this->_REQUEST("fax");
		$email     = $this->_REQUEST("email");
		$address   = $this->_REQUEST("address");	
		$sdt1   	   = $this->_REQUEST("sdt1");
		$edt1   	   = $this->_REQUEST("edt1");
		$where_str = "s.shop_id != 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");	
		if( !empty($name) ){
			$where_str .=" and s.shop_name like '%$name%'";
		}	
		if( !empty($address) ){
			$where_str .=" and s.address like '%$address%'";
		}	
		if( !empty($sdt1) ){
			$where_str .=" and s.adt >= '$sdt1'";
		}			
		if( !empty($edt1) ){
			$where_str .=" and s.adt < '$edt1'";
		}		
		
		if( $status=='-1'){
			$where_str .=" and s.status='0'";
		}else if (!empty($status)){
			$where_str .=" and s.status='$status'";	
		}
		
		//排序生成
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_price' ){
			$order_by .=" s.price $orderDirection";
		}else if( $orderField=='by_stock' ){
			$order_by .=" s.stock $orderDirection";
		}else if( $orderField=='by_sort' ){
			$order_by .=" sort $orderDirection";
		}else if( $orderField=='by_stock' ){
			$order_by .=" s.stock $orderDirection";
		}else if( $orderField=='by_stock' ){
			$order_by .=" s.stock $orderDirection";
		}else{
			$order_by .=" s.shop_id desc";
		}
		
		//**************************************************************************
		$countSql    = "select shop_id from fly_shop as s where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select * from fly_shop as s where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$status		 =$this->shop_status();
		foreach($list as $key=>$row){
			$list[$key]['member_name']=$this->member->member_get_name($row['member_id']);
			$list[$key]['type_name']=$this->type->shop_type_get_name($row['type_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
		return $assignArray;
		
	}
	public function shop_json(){
		$assArr  = $this->shop();
		echo json_encode($assArr);
	}	
	public function shop_show(){
			$assArr  = $this->shop();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/shop_show.html');	
	}
	public function shop_lookup_search(){
			$assArr  		= $this->shop();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/shop_lookup_search.html');	
	}	
	public function shop_show_one(){
		$id	 = $this->_REQUEST("id");
		$sql = "select * from fly_shop where id='$id'";
		$one = $this->C($this->cacheDir)->findOne($sql);	
		$one['members_name']=$this->member->member_get_name($one['members']);
		$one['type_name']=$this->type->shop_type_get_name($one['type_id']);
		$one['status_name']=$this->shop_status($one['status']);
		$smarty 	= $this->setSmarty();
		$smarty->assign(array("one"=>$one));
		$smarty->display('admin/shop_show_one.html');	
	}

	public function shop_add(){
		if(empty($_POST)){
			$type_opt=$this->type->shop_type_get_opt('type_id');
			$member_list=$this->member->member_list();
			$smarty = $this->setSmarty();
			$smarty->assign(array('type_opt'=>$type_opt,'member_list'=>$member_list));
			$smarty->display('admin/shop_add.html');	
		}else{
			$this->shop_add_save();
			$this->location("操作成功","/admin/Shop/shop_show/");	
		}
	}	
	public function shop_add_save(){
		$shop_name	= $this->_REQUEST("shop_name");
		$type_id	= $this->_REQUEST("type_id");
		$member_id	= $this->_REQUEST("member_id");
		$intro	 	= $this->_REQUEST("intro");
		$tel	 = $this->_REQUEST("tel");
		$mobile	 = $this->_REQUEST("mobile");
		$address = $this->_REQUEST("address");
		$zipcode = $this->_REQUEST("zipcode");
		$email  = $this->_REQUEST("email");
		$status	 =1;
		$dt	 = date("Y-m-d H:i:s",time());
		$sql = "insert into fly_shop(shop_name,type_id,member_id,tel,mobile,zipcode,email,address,intro,status,adt) 
							values('$shop_name','$type_id','$member_id','$tel','$mobile','$zipcode','$email','$address','$intro','$status','$dt');";
		$rtn=$this->C($this->cacheDir)->update($sql);
		if($rtn>0){
			return true;
		}else{
			return false;
		}
	}
	
	public function shop_modify(){
		$shop_id = $this->_REQUEST("shop_id");
		if(empty($_POST)){
			$sql 		= "select * from fly_shop where shop_id='$shop_id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$type_opt	=$this->type->shop_type_get_opt('type_id',$one['type_id']);
			$one['member_name']=$this->member->member_get_name($one['member_id']);
			$one['member_list']=$this->member->member_list();
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"type_opt"=>$type_opt));
			$smarty->display('admin/shop_modify.html');	
		}else{//更新保存数据
			$shop_id	= $this->_REQUEST("shop_id");
			$shop_name 	= $this->_REQUEST("shop_name");
			$type_id	= $this->_REQUEST("type_id");
			$member_id	= $this->_REQUEST("member_id");
			$intro	 	= $this->_REQUEST("intro");
			$content	= $this->_REQUEST("content");
			$tel	 = $this->_REQUEST("tel");
			$mobile	 = $this->_REQUEST("mobile");
			$address = $this->_REQUEST("address");
			$zipcode = $this->_REQUEST("zipcode");
			$email = $this->_REQUEST("email");
			$sql= "update fly_shop set 
							shop_name='$shop_name',
							type_id='$type_id',
							member_id='$member_id',
							intro='$intro',
							content='$content',
							tel='$tel',
							mobile='$mobile',
							address='$address',
							zipcode='$zipcode',
							email='$email'
			 		where shop_id='$shop_id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->location("操作成功","/admin/Shop/shop_show/");		
		}
	}
	
	public function shop_status(){
		$rtn=array(
			"0"=>"待审核",
			"1"=>"已审核",
			"2"=>"未通过"
		);
		return $rtn;
	}
	
		
	public function shop_del(){
		$shop_id= $this->_REQUEST("shop_id");
		$sql  = "delete from fly_shop where shop_id in ($shop_id)";
		$this->C($this->cacheDir)->update($sql);	
		$rtnArr=array('rtnstatus'=>'success','msg'=>'删除成功');
		echo json_encode($rtnArr);
	}
	
	public function shop_pass(){
		$shop_id= $this->_REQUEST("shop_id");
		$sql  	= "update fly_shop set status='1' where shop_id in ($shop_id)";
		$this->C($this->cacheDir)->update($sql);

		$content="你申请的店铺已经通过了";
		$this->shop_add_notice($shop_id,$content);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'操作成功');
		echo json_encode($rtnArr);
	}
	public function shop_reject(){
		$shop_id= $this->_REQUEST("shop_id");
		$sql  	= "update fly_shop set status='2' where shop_id in ($shop_id)";
		$this->C($this->cacheDir)->update($sql);
		$content="你申请的店铺审核暂未通过";
		$this->shop_add_notice($shop_id,$content);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'操作成功');
		echo json_encode($rtnArr);
	}

	public function shop_add_notice($id,$content){
		$sql	="select member_id from fly_shop where shop_id in ($id);";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		foreach($list as $row){
			$this->L('admin/MemberNotice')->member_notice_add_save($row['member_id'],$content);
		}
	}
	
	//传入ID返回名字
	public function shop_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,name from fly_shop where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."";
			}
		}
		return $str;
	}
	
	
	
}//
?>