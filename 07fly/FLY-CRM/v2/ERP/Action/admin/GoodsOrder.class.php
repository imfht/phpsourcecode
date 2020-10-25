<?php
/*
 *
 * admin.GoodsOrder  产品订单   
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
class GoodsOrder extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
		$this->shop=_instance('Action/admin/Shop');
		$this->address=_instance('Action/admin/MemberAddress');
		$this->member=_instance('Action/admin/Member');
		$this->goods=_instance('Action/goods/Goods');
		$this->order_list=_instance('Action/admin/GoodsOrderList');
		
	}	
	public function goods_order(){
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
		$status   = $this->_REQUEST("status");
		$trade     = $this->_REQUEST("org2_id");
		$fax   	   = $this->_REQUEST("fax");
		$email     = $this->_REQUEST("email");
		$address   = $this->_REQUEST("address");	
		$bdt   	   = $this->_REQUEST("bdt");
		$edt   	   = $this->_REQUEST("edt");
		$where_str = " o.order_id != 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and o.$searchKeyword like '%$searchValue%'";
		}
		if($status=='-1'){
			$where_str .=" and o.status = '0'";
		}else if(!empty($status)){
			$where_str .=" and o.status = '$status'";	
		}
		if( !empty($bdt) ){
			$where_str .=" and o.adt >= '$bdt'";
		}	
		if( !empty($edt) ){
			$where_str .=" and o.adt < '$edt'";
		}		
		//**************************************************************************
		$countSql 	= "select * from fly_goods_order as o
						where $where_str";
		$totalCount = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		= "select o.* from fly_goods_order as o 
						where $where_str order by order_id desc limit $beginRecord,$pageSize";
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['buyer_member_name']=$this->member->member_get_name($row['buyer_member_id']);
			$list[$key]['order_list']=$this->order_list->goods_order_list($row['order_id']);
			//$list[$key]['type_name']=$this->type->shop_type_get_name($row['type_id']);
			$list[$key]['address']=$this->address->member_address_get_one($row['address_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
		
	}
	public function goods_order_json(){
		$assArr  = $this->goods_order();
		echo json_encode($assArr);
	}	
	public function goods_order_show(){
			$assArr  = $this->goods_order();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/goods_order_show.html');	
	}

	public function goods_order_show_one(){
		$id	 = $this->_REQUEST("id");
		$sql = "select * from fly_goods_order where id='$id'";
		$one = $this->C($this->cacheDir)->findOne($sql);	
		$one['members_name']=$this->member->member_get_name($one['members']);
		$one['type_name']=$this->type->goods_order_type_get_name($one['type_id']);
		$one['status_name']=$this->goods_order_status($one['status']);
		$smarty 	= $this->setSmarty();
		$smarty->assign(array("one"=>$one));
		$smarty->display('admin/goods_order_show_one.html');	
	}

	public function goods_order_add(){
		if(empty($_POST)){
			$shop_opt=$this->type->shop_type_get_opt('type_id');
			$smarty = $this->setSmarty();
			$smarty->assign(array('shop_opt'=>$shop_opt));
			$smarty->display('admin/goods_order_add.html');	
		}else{
			if($this->goods_order_add_save()){
				$this->L("Common")->ajax_json_success("操作成功","2","/admin/GoodsOrder/goods_order_show/");
			}
				
		}
	}	
	public function goods_order_add_save(){
		$name	 	= $this->_REQUEST("name");
		$shop_id	= $this->_REQUEST("shop_id");
		$type_id	= $this->_REQUEST("type_id");
		$member_id	= $this->_REQUEST("member_id");
		$intro	 	= $this->_REQUEST("intro");
		$price	 = $this->_REQUEST("price");
		$status	 =1;
		$dt	 = date("Y-m-d H:i:s",time());
		$sql = "insert into fly_goods_order(name,shop_id,type_id,member_id,price,intro,status,adt) 
							values('$name','$shop_id','$type_id','$member_id','$price','$intro','$status','$dt');";
		$rtn=$this->C($this->cacheDir)->update($sql);
		if($rtn>0){
			return true;
		}else{
			return false;
		}
	}
	
	public function goods_order_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_goods_order where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$one['type_name']	=$this->type->shop_type_get_name($one['type_id']);
			$one['member_name']=$this->member->member_get_name($one['member_id']);
			$one['shop_name']=$this->shop->shop_get_name($one['shop_id']);
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one));
			$smarty->display('admin/goods_order_modify.html');	
		}else{//更新保存数据
			$id	 		= $this->_REQUEST("id");
			$name	 	= $this->_REQUEST("name");
			$type_id	= $this->_REQUEST("type_id");
			$member_id	= $this->_REQUEST("member_id");
			$shop_id	= $this->_REQUEST("shop_id");
			$intro	 	= $this->_REQUEST("intro");
			$price	= $this->_REQUEST("price");
			$sql= "update fly_goods_order set 
							name='$name',
							type_id='$type_id',
							member_id='$member_id',
							shop_id='$shop_id',
							intro='$intro',
							price='$price'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","2","/admin/GoodsOrder/goods_order_show/");		
		}
	}
		
	public function goods_order_del(){
		$order_id = $this->_REQUEST("order_id");
		$sql  = "delete from fly_goods_order where order_id in ($order_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/admin/GoodsOrder/goods_order_show/");	
	}

	//传入ID返回名字
	public function goods_order_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,account as name from fly_goods_order where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
	
	//传入ID返回名字
	public function goods_order_group_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,name from fly_goods_order where expert_group_id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
	
	//修改为线下支付
	public function goods_order_pay_mode(){
		$order_id=$this->_REQUEST('order_id');	
		$upt_data=array(
					'pay_mode'=>'1',
					'status'=>'1',
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_order',$upt_data,"order_id in($order_id)",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);		
	}
	//关闭交易
	public function goods_order_close(){
		$order_id=$this->_REQUEST('order_id');	
		$upt_data=array(
					'status'=>'10'
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_order',$upt_data,"order_id in($order_id)",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);		
	}
	//确认收货
	public function goods_order_received(){
		$order_id=$this->_REQUEST('order_id');	
		$upt_data=array(
					'status'=>'3'
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_order',$upt_data,"order_id in($order_id)",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);		
	}
	//订单备注
	public function goods_order_remarks(){
		$order_id=$this->_REQUEST('order_id');
		if(empty($_POST)){
			$sql ="select order_id,remarks from fly_goods_order where order_id='$order_id'";	
			$one =$this->C($this->cacheDir)->findOne($sql);			
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('admin/goods_order_remarks.html');	
		}else{
			$remarks	=$this->_REQUEST('remarks');	
			$upt_data=array(
						'remarks'=>$remarks
					 );
			$this->C( $this->cacheDir )->modify('fly_goods_order',$upt_data,"order_id='$order_id'",true);
			$rtnArr=array('rtnstatus'=>'success','msg'=>'');
			echo json_encode($rtnArr);	
				
		}
	}
	//订单发货
	public function goods_order_send(){
		$order_id=$this->_REQUEST('order_id');
		if(empty($_POST)){
			$sql ="select * from fly_goods_order where order_id='$order_id'";	
			$one =$this->C($this->cacheDir)->findOne($sql);			
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('admin/goods_order_send.html');	
		}else{	
			$upt_data=array(
						'express_name'=>$this->_REQUEST('express_name'),
						'express_number'=>$this->_REQUEST('express_number'),
						'status'=>'2'//2=已经发货
					 );
			$this->C( $this->cacheDir )->modify('fly_goods_order',$upt_data,"order_id='$order_id'",true);
			$rtnArr=array('rtnstatus'=>'success','msg'=>'');
			echo json_encode($rtnArr);	
				
		}
	}
	//修改价格
	public function goods_order_modify_price(){
		$order_id=$this->_REQUEST('order_id');
		if(empty($_POST)){
			$sql ="select * from fly_goods_order where order_id='$order_id'";	
			$one =$this->C($this->cacheDir)->findOne($sql);	
			$one['order_list']=$this->order_list->goods_order_list($one['order_id']);
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('admin/goods_order_modify_price.html');	
		}else{
			$goods_money=$this->_REQUEST('goods_money');
			$shipping_money=$this->_REQUEST('shipping_money');
			$pay_money=$this->_REQUEST('pay_money');
			$upt_data=array(
						'goods_money'=>$this->_REQUEST('goods_money'),
						'shipping_money'=>$this->_REQUEST('shipping_money'),
						'pay_money'=>$this->_REQUEST('pay_money'),
						'order_money'=>$this->_REQUEST('pay_money'),
					 );
			$this->C( $this->cacheDir )->modify('fly_goods_order',$upt_data,"order_id='$order_id'",true);
			
			$order_list_id=$this->_REQUEST('order_list_id');
			$sku_price=$this->_REQUEST('sku_price');
			$adjust_money=$this->_REQUEST('adjust_money');
			foreach($order_list_id as $i=>$value){
				$id=$value;
				$g_m=$sku_price[$i]+$adjust_money[$i];
				$a_m=$adjust_money[$i];
				$upt_data=array(
						'goods_money'=>$g_m,
						'adjust_money'=>$a_m
					 );
				$this->C( $this->cacheDir )->modify('fly_goods_order_list',$upt_data,"order_list_id='$id'",true);
			}
			$rtnArr=array('rtnstatus'=>'success','msg'=>'');
			echo json_encode($rtnArr);	
				
		}
	}
	//修改地址
	public function goods_order_modify_address(){
		$order_id=$this->_REQUEST('order_id');
		if(empty($_POST)){
			$sql ="select * from fly_goods_order where order_id='$order_id'";	
			$one =$this->C($this->cacheDir)->findOne($sql);			
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('admin/goods_order_modify_address.html');	
		}else{
			$upt_data=array(
						'receiver_name'=>$this->_REQUEST('receiver_name'),
						'receiver_mobile'=>$this->_REQUEST('receiver_mobile'),
						'receiver_address'=>$this->_REQUEST('receiver_address'),
					 );
			$this->C( $this->cacheDir )->modify('fly_goods_order',$upt_data,"order_id='$order_id'",true);
			$rtnArr=array('rtnstatus'=>'success','msg'=>'');
			echo json_encode($rtnArr);	
				
		}
	}	
	//订单详细
	public function goods_order_detail(){
		$order_id=$this->_REQUEST('order_id');
		if(empty($_POST)){
			$sql ="select * from fly_goods_order where order_id='$order_id'";	
			$one =$this->C($this->cacheDir)->findOne($sql);
			$one['order_list']=$this->order_list->goods_order_list($one['order_id']);
			$one['buyer_member_name']=$this->member->member_get_name($one['buyer_member_id']);
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('admin/goods_order_detail.html');	
		}else{
			$remarks	=$this->_REQUEST('remarks');	
			$upt_data=array(
						'remarks'=>$remarks
					 );
			$this->C( $this->cacheDir )->modify('fly_goods_order',$upt_data,"order_id='$order_id'",true);
			$rtnArr=array('rtnstatus'=>'success','msg'=>'');
			echo json_encode($rtnArr);	
				
		}
	}
}//
?>