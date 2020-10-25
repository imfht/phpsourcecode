<?php
/*
 *
 * admin.GoodsBrand  商品品牌   
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
class GoodsBrand extends Action {

	private $cacheDir = ''; //缓存目录
	private $auth;
	private $attrValue='';
	public function __construct() {
		$this->auth = _instance( 'Action/sysmanage/Auth' );
		$this->comm_front = _instance( 'Action/CommFront' );
	}

	public function goods_brand() {
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//条件生成
		//*********************
		$where_str = "brand_id != 0";
		
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField!='' ){
			$order_by .=" $orderField $orderDirection";
		}else{
			$order_by .=" sort desc";
		}
		
		//**************************************************************************
		//根据条件查询出所有数据
		$countSql    = "select * from fly_goods_brand where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql	= "select * from fly_goods_brand  where $where_str $order_by limit $beginRecord,$pageSize";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		foreach($list as $key=>$row){
			$list[$key]['brand_pic_img']=$this->comm_front->get_images_url($row['brand_pic']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,'value'=>$list,'code'=>'200');	
		return $assignArray;
	}
	//得到数据json格式
	public function goods_brand_json() {
		$list	=$this->goods_brand();
		echo json_encode($list);
	}
	public function goods_brand_show() {
		$list =$this->goods_brand();
		$smarty = $this->setSmarty();
		$smarty->assign(array( "list" => $list) );
		$smarty->display('goods/goods_brand_show.html');
	}

	public function goods_brand_add() {
		if ( empty( $_POST ) ) {
			$smarty = $this->setSmarty();
			$smarty->display( 'goods/goods_brand_add.html' );
		} else {	
			$post_data=array(
					'brand_name'=>$this->_REQUEST( "brand_name" ),
					'brand_pic'=>$this->_REQUEST( "brand_pic" ),
					'brand_initial'=>$this->_REQUEST( "brand_initial" ),
					'brand_recommend'=>$this->_REQUEST( "brand_recommend" ),
					'sort'=>$this->_REQUEST( "sort" )
				 );
			$this->C( $this->cacheDir )->insert('fly_goods_brand',$post_data );
			$this->location( "操作成功", "/goods/GoodsBrand/goods_brand_show/" );
		}
	}
	
	public function goods_brand_modify() {
		$brand_id = $this->_REQUEST( "brand_id" );
		if ( empty( $_POST ) ) {
			$sql = "select * from fly_goods_brand where brand_id='$brand_id'";
			$one = $this->C( $this->cacheDir )->findOne( $sql );
			$smarty = $this->setSmarty();
			$smarty->assign( array( "one" => $one) );
			$smarty->display( 'goods/goods_brand_modify.html' );
		} else {
			$brand_id=$this->_REQUEST('brand_id');
			$upt_data=array(
					'brand_name'=>$this->_REQUEST( "brand_name" ),
					'brand_pic'=>$this->_REQUEST( "brand_pic" ),
					'brand_initial'=>$this->_REQUEST( "brand_initial" ),
					'brand_recommend'=>$this->_REQUEST( "brand_recommend" ),
					'sort'=>$this->_REQUEST( "sort" )
				 );
			$this->C( $this->cacheDir )->modify('fly_goods_brand',$upt_data,"brand_id='$brand_id'",true);			
			$this->location( "操作成功", "/goods/GoodsBrand/goods_brand_show/" );
		}
	}
	public function goods_brand_modify_sort() {
		$brand_id=$this->_REQUEST('brand_id');	
		$sort	=$this->_REQUEST('sort');	
		$upt_data=array(
					'sort'=>$this->_REQUEST( "sort" )
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_brand',$upt_data,"brand_id='$brand_id'",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}
	public function goods_brand_modify_recommend() {
		$brand_id=$this->_REQUEST('brand_id');	
		$sort	=$this->_REQUEST('sort');	
		$upt_data=array(
					'brand_recommend'=>$this->_REQUEST( "brand_recommend" )
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_brand',$upt_data,"brand_id='$brand_id'",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}	

	public function goods_brand_del() {
		$brand_id = $this->_REQUEST( "brand_id" );
		$sql = "delete from fly_goods_brand where brand_id='$brand_id'";
		$this->C( $this->cacheDir )->update( $sql );
		$this->location( "操作成功", "/goods/GoodsBrand/goods_brand_show/" );
	}



} //
?>