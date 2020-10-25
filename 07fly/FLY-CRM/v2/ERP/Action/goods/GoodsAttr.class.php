<?php
 /*
 *
 * admin.GoodsAttr 商品属性表   
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
class GoodsAttr extends Action {

	private $cacheDir = ''; //缓存目录
	private $auth;
	private $attrValue='';
	public function __construct() {
		$this->auth = _instance( 'Action/sysmanage/Auth' );
		$this->attrValue = _instance( 'Action/goods/GoodsAttrValue' );
	}

	public function goods_attr() {
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//条件生成
		//*********************
		
		$where_str = "attr_id != 0";
		
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
		$countSql    = "select * from fly_goods_attr where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql	= "select * from fly_goods_attr  where $where_str $order_by limit $beginRecord,$pageSize";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		foreach($list as $key=>$row){
			$list[$key]['attr_value_name']=$this->attrValue->goods_attr_value_name($row['attr_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function goods_attr_json() {
		$list	=$this->goods_attr();
		echo json_encode($list);
	}
	public
	function goods_attr_show() {
		$list =$this->goods_attr();
		$smarty = $this->setSmarty();
		$smarty->assign(array( "list" => $list) );
		$smarty->display('goods/goods_attr_show.html');
	}

	public function goods_attr_add() {
		if ( empty( $_POST ) ) {
			$smarty = $this->setSmarty();
			$smarty->display( 'goods/goods_attr_add.html' );
		} else {	
			$attr_value_data['attr_value_sort'] = $this->_REQUEST( "attr_value_sort" );
			$attr_value_data['attr_value_name'] = $this->_REQUEST( "attr_value_name" );
			$attr_value_data['attr_value_data'] = $this->_REQUEST( "attr_value_data" );
			$attr_value_data['attr_value_type'] = $this->_REQUEST( "attr_value_type" );
			$attr_value_data['attr_value_is_search'] = $this->_REQUEST( "attr_value_is_search" );
			$post_data=array(
					'attr_name'=>$this->_REQUEST( "attr_name" ),
					'visible'=>$this->_REQUEST( "visible" ),
					'sort'=>$this->_REQUEST( "sort" )
				 );
			$attr_value_data['attr_id']=$this->C( $this->cacheDir )->insert('fly_goods_attr',$post_data );
			$this->attrValue->goods_attr_value_add($attr_value_data);
			$this->location( "操作成功", "/goods/GoodsAttr/goods_attr_show/" );
		}
	}
	
	public
	function goods_attr_modify() {
		$id = $this->_REQUEST( "id" );
		if ( empty( $_POST ) ) {
			$sql = "select * from fly_goods_attr where attr_id='$id'";
			$one = $this->C( $this->cacheDir )->findOne( $sql );
			$goods_attr_value_list=$this->attrValue->goods_attr_value_list($one['attr_id']);
			$smarty = $this->setSmarty();
			$smarty->assign( array( "one" => $one,"goods_attr_value_list" => $goods_attr_value_list) );
			$smarty->display( 'goods/goods_attr_modify.html' );
		} else {
			$attr_id=$this->_REQUEST('attr_id');
			$attr_value_data['attr_value_sort'] = $this->_REQUEST( "attr_value_sort" );
			$attr_value_data['attr_value_name'] = $this->_REQUEST( "attr_value_name" );
			$attr_value_data['attr_value_data'] = $this->_REQUEST( "attr_value_data" );
			$attr_value_data['attr_value_type'] = $this->_REQUEST( "attr_value_type" );
			$attr_value_data['attr_value_is_search'] = $this->_REQUEST( "attr_value_is_search" );
			$attr_value_data['attr_id'] = $attr_id;
	
			$upt_data=array(
					'attr_name'=>$this->_REQUEST( "attr_name" ),
					'visible'=>$this->_REQUEST( "visible" ),
					'sort'=>$this->_REQUEST( "sort" )
				 );
			$this->C( $this->cacheDir )->modify('fly_goods_attr',$upt_data,"attr_id='$attr_id'",true);
			$this->attrValue->goods_attr_value_add($attr_value_data);
			
			$this->location( "操作成功", "/goods/GoodsAttr/goods_attr_show/" );
		}
	}
	
	public function goods_attr_del() {
		$id = $this->_REQUEST( "id" );
		$sql = "delete from fly_goods_attr where attr_id='$id'";
		$this->C( $this->cacheDir )->update( $sql );
		$this->location( "操作成功", "/goods/GoodsAttr/goods_attr_show/" );
	}
	
	//是否启用
	public function goods_attr_modify_visible() {
		$id=$this->_REQUEST('id');	
		$upt_data=array(
					'visible'=>$this->_REQUEST( "visible" )
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_attr',$upt_data,"attr_id='$id'",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}	
	//更排序
	public function goods_attr_modify_sort() {
		$id=$this->_REQUEST('id');	
		$upt_data=array(
					'sort'=>$this->_REQUEST( "sort" )
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_attr',$upt_data,"attr_id='$id'",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}	

} //
?>