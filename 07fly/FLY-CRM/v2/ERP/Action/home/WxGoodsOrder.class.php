<?php
/*
 *
 * home.GoodsOrder  商品订单管理   
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
class WxGoodsOrder extends Action {
	private $cacheDir = ''; //缓存目录
	private $type = ''; //缓存目录
	private $member = ''; //缓存目录
	private $shop = ''; //缓存目录
	public
	function __construct() {
		$this->auth = _instance( 'Action/home/WxAuth' );
		$this->member = _instance( 'Action/home/WxMember' );
		$this->order_list = _instance( 'Action/home/WxGoodsOrderList' );
	}

	//我的订单
	public
	function goods_order_my() {
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = 10;//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$beginRecord = ($currentPage-1)*$numPerPage;
		
		
		$member = $this->L( 'home/WxMember' )->member_get_info();
		$member_id = $member[ 'member_id' ];
		$status = $this->_REQUEST( "status" );
		//待发货
		$sql 	= "select * from fly_goods_order where buyer_member_id='$member_id' and status='$status' order by order_id desc limit $beginRecord,$numPerPage";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		$status = $this->goods_order_status();
		foreach($list as $key=>$row){
			$list[$key]['buyer_member_name']=$this->member->member_get_name($row['buyer_member_id']);
			$list[$key]['order_list']=$this->order_list->goods_order_list($row['order_id']);
			$list[ $key ][ 'status_name' ] = $status[ $row[ 'status' ] ];
		}
		if(empty($list)){ $list='null';}
		return $list;
	}
	
	public function goods_order_my_json(){
		$assArr  = $this->goods_order_my();
		$rtnArr  =array('code'=>'sucess','message'=>'加载数据','list'=>$assArr);
		echo json_encode($rtnArr);		
	}
	
	public function goods_order_my_show(){
		$smarty = $this->setSmarty();
		$smarty->display( 'home/goods_order_my.html' );	
	}
	
	//我的详细
	public
	function goods_order_my_info() {
		$member = $this->L( 'home/WxMember' )->member_get_info();
		$member_id = $member[ 'id' ];
		$id = $this->_REQUEST( "id" );
		$sql = "select o.*,p.name from fly_goods_order as o left join fly_product as p on o.product_id=p.id 
					where o.id='$id'";

		$one = $this->C( $this->cacheDir )->findOne( $sql );
		$status = $this->goods_order_status();
		$one[ 'status_name' ] = $status[ $one[ 'status' ] ];
		$smarty = $this->setSmarty();
		$smarty->assign( array( "one" => $one ) );
		$smarty->display( 'home/goods_order_my_info.html' );
	}

	//我店铺订单
	public
	function goods_order_shop() {
		$member = $this->L( 'home/WxMember' )->member_get_info();
		$member_id = $member[ 'id' ];
		$status = $this->goods_order_status();
		
		//待发货
		$sql_1	= "select o.*,p.name from fly_goods_order as o left join fly_product as p on o.product_id=p.id 
					where p.member_id='$member_id' and o.status='1' order by id desc";
		$list_1 = $this->C( $this->cacheDir )->findAll( $sql_1 );
		foreach ( $list_1 as $key => $row ) {
			$list_1[ $key ][ 'status_name' ] = $status[ $row[ 'status' ] ];
		}
		
		//待收货
		$sql_2 = "select o.*,p.name from fly_goods_order as o left join fly_product as p on o.product_id=p.id 
					where p.member_id='$member_id' and o.status='2' order by id desc";
		$list_2 = $this->C( $this->cacheDir )->findAll( $sql_2 );
		foreach ( $list_2 as $key => $row ) {
			$list_2[ $key ][ 'status_name' ] = $status[ $row[ 'status' ] ];
		}

		$sql_3 = "select o.*,p.name from fly_goods_order as o left join fly_product as p on o.product_id=p.id 
					where p.member_id='$member_id' and o.status='3' order by id desc";
		$list_3 = $this->C( $this->cacheDir )->findAll( $sql_3 );
		foreach ( $list_3 as $key => $row ) {
			$list2[ $key ][ 'status_name' ] = $status[ $row[ 'status' ] ];
		}
		
		//余额积分
		$sql_4 	= "select * from fly_goods_order_log where member_sell_id='$member_id' order by id desc;";
		$list_4 = $this->C( $this->cacheDir )->findAll( $sql_4 );
		
		$smarty = $this->setSmarty();
		$smarty->assign( array( "list_1" => $list_1,"list_2" => $list_2,"list_3" => $list_3,"list_4" => $list_4 ) );
		$smarty->display( 'home/goods_order_shop.html' );
	}
	//我的详细
	public
	function goods_order_shop_info() {
		$member = $this->L( 'home/WxMember' )->member_get_info();
		$member_id = $member[ 'id' ];
		$id = $this->_REQUEST( "id" );
		$sql = "select o.*,p.name,p.shop_id from fly_goods_order as o left join fly_product as p on o.product_id=p.id 
					where o.id='$id'";

		$one = $this->C( $this->cacheDir )->findOne( $sql );
		
		$shop_id=$one['shop_id'];
		$address_id=$one['address_id'];
		
		$shop_sql="select * from fly_shop where id='$shop_id'";
		$shop_one= $this->C( $this->cacheDir )->findOne( $shop_sql );
		
		$addr_sql="select * from fly_member_address where id='$address_id'";
		$addr_one= $this->C( $this->cacheDir )->findOne( $addr_sql );
		
		$status = $this->goods_order_status();
		$one[ 'status_name' ] = $status[ $one[ 'status' ] ];
		$smarty = $this->setSmarty();
		$smarty->assign( array( "one" => $one ,"shop_one" => $shop_one,"addr_one" => $addr_one ) );
		$smarty->display( 'home/goods_order_shop_info.html' );
	}

	public
	function goods_order_status() {
		$rtn = array(
			"0" => "待付款",
			"1" => "待发货",
			"2" => "待收货",
			"3" => "交易完成"
		);
		return $rtn;
	}

	public
	function goods_order_send() {
		$id = $this->_REQUEST( "id" );
		$status = $this->_REQUEST( "status" );
		$sql = "update fly_goods_order set status='$status' where id='$id';";
		$rtn = $this->C( $this->cacheDir )->update( $sql );
		if ( $rtn > 0 ) {
			$rtn_msg = array( 'code' => 'sucess', 'message' => '申请成功' );
		} else {
			$rtn_msg = array( 'code' => 'fail', 'message' => '申请失败' );
		}
		echo json_encode( $rtn_msg );
	}


} //
?>