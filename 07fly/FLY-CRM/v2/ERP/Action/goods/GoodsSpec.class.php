<?php
/*
 *
 * admin.GoodsSpec  商品规格管理   
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
class GoodsSpec extends Action {

	private $cacheDir = ''; //缓存目录
	private $auth;
	private $specValue='';
	public function __construct() {
		$this->auth = _instance( 'Action/sysmanage/Auth' );
		$this->specValue = _instance( 'Action/goods/GoodsSpecValue' );
	}

	public function goods_spec() {
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//条件生成
		//*********************
		
		$where_str = "spec_id != 0";
		
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
		$countSql    = "select * from fly_goods_spec where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql	= "select * from fly_goods_spec  where $where_str $order_by limit $beginRecord,$pageSize";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		foreach($list as $key=>$row){
			$list[$key]['spec_value_name']=$this->specValue->goods_spec_value_name($row['spec_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function goods_spec_json() {
		$list	=$this->goods_spec();
		echo json_encode($list);
	}

	public
	function goods_spec_show() {
		$list =$this->goods_spec();
		$smarty = $this->setSmarty();
		$smarty->assign(array( "list" => $list) );
		$smarty->display('goods/goods_spec_show.html');
	}
	//获取生成数据
	public function goods_spec_create(){
		$create_id =$this->_REQUEST("create_id");
		$where_str = "spec_id != 0";
		$sql	= "select * from fly_goods_spec  where $where_str order by sort asc";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		foreach($list as $key=>$row){
			$list[$key]['spec_value_name']=$this->specValue->goods_spec_value_name($row['spec_id']);
			$list[$key]['spec_value_list']=$this->specValue->goods_spec_value_list($row['spec_id']);
		}		
		$smarty = $this->setSmarty();
		$smarty->assign(array( "list" => $list,"create_id" => $create_id) );
		$smarty->display('goods/goods_spec_create.html');
	}
	
	public function goods_spec_create_json(){
		$list=$this->_REQUEST('chk_value_txt');
		$list=json_decode($list,true);
		$spec=array();
		//按规格类型整理规格值
		foreach($list as $key=>$row){
			$spec[$row['spec_id']]['sku_name'][]=$row['spec_name'].':'.$row['spec_value_name'];
			$spec[$row['spec_id']]['sku_id'][]=$row['spec_id'].':'.$row['spec_value_id'];
		}
		//分类规格数据
		$spec_name=array();
		$spec_id=array();
		foreach($spec as $row){
			$spec_name[]=$row['sku_name'];
			$spec_id[]=$row['sku_id'];
		}
		//规格笛卡尔生成
		$dikaer_name=$this->goods_spec_create_dikaer($spec_name);
		$dikaer_id	=$this->goods_spec_create_dikaer($spec_id);
		foreach($dikaer_name as $i=>$name){
			$rtnArr[$i]['txt_name'] =$name;
			$rtnArr[$i]['txt_id']  =$dikaer_id[$i];
			
		}
		echo json_encode($rtnArr);
	}

	//规格笛卡尔
	function goods_spec_create_dikaer($arr){
		$arr1 = array();
		$result = array_shift($arr);
		while($arr2 = array_shift($arr)){
			$arr1 = $result;
			$result = array();
			foreach($arr1 as $v){
				foreach($arr2 as $v2){
					$result[] = $v.','.$v2;
				}
			}
		}
	  return $result;
	}

	//增加
	public function goods_spec_add() {
		if ( empty( $_POST ) ) {
			$smarty = $this->setSmarty();
			$smarty->display( 'goods/goods_spec_add.html' );
		} else {	
			$spec_value_data['spec_value_sort'] = $this->_REQUEST( "spec_value_sort" );
			$spec_value_data['spec_value_name'] = $this->_REQUEST( "spec_value_name" );
			$post_data=array(
					'spec_name'=>$this->_REQUEST( "spec_name" ),
					'visible'=>$this->_REQUEST( "visible" ),
					'sort'=>$this->_REQUEST( "sort" )
				 );
			$spec_value_data['spec_id']=$this->C( $this->cacheDir )->insert('fly_goods_spec',$post_data );
			$this->specValue->goods_spec_value_add($spec_value_data);
			$this->location( "操作成功", "/goods/GoodsSpec/goods_spec_show/" );
		}
	}
	
	public
	function goods_spec_modify() {
		$id = $this->_REQUEST( "id" );
		if ( empty( $_POST ) ) {
			$sql = "select * from fly_goods_spec where spec_id='$id'";
			$one = $this->C( $this->cacheDir )->findOne( $sql );
			$goods_spec_value_list=$this->specValue->goods_spec_value_list($one['spec_id']);
			$smarty = $this->setSmarty();
			$smarty->assign( array( "one" => $one,"goods_spec_value_list" => $goods_spec_value_list) );
			$smarty->display( 'goods/goods_spec_modify.html' );
		} else {
			$spec_id=$this->_REQUEST('spec_id');
			$spec_value_data['spec_value_sort'] = $this->_REQUEST( "spec_value_sort" );
			$spec_value_data['spec_value_name'] = $this->_REQUEST( "spec_value_name" );
			$spec_value_data['spec_value_data'] = $this->_REQUEST( "spec_value_data" );
			$spec_value_data['spec_value_type'] = $this->_REQUEST( "spec_value_type" );
			$spec_value_data['spec_value_is_search'] = $this->_REQUEST( "spec_value_is_search" );
			$spec_value_data['spec_id'] = $spec_id;
	
			$upt_data=array(
					'spec_name'=>$this->_REQUEST( "spec_name" ),
					'visible'=>$this->_REQUEST( "visible" ),
					'sort'=>$this->_REQUEST( "sort" )
				 );
			$this->C( $this->cacheDir )->modify('fly_goods_spec',$upt_data,"spec_id='$spec_id'",true);
			$this->attrValue->goods_spec_value_add($spec_value_data);
			
			$this->location( "操作成功", "/goods/GoodsSpec/goods_spec_show/" );
		}
	}
	
	//规格删除
	public
	function goods_spec_del() {
		$id = $this->_REQUEST( "id" );
		$sql = "delete from fly_goods_spec where spec_id='$id'";
		$this->C( $this->cacheDir )->update( $sql );
		$this->specValue->goods_spec_value_del($id);
		$this->location( "操作成功", "/goods/GoodsSpec/goods_spec_show/" );
	}

	//是否启用
	public
	function goods_spec_modify_visible() {
		$id=$this->_REQUEST('id');	
		$upt_data=array(
					'visible'=>$this->_REQUEST( "visible" )
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_spec',$upt_data,"spec_id='$id'",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}	
	//更排序
	public
	function goods_spec_modify_sort() {
		$id=$this->_REQUEST('id');	
		$upt_data=array(
					'sort'=>$this->_REQUEST( "sort" )
				 );
		$this->C( $this->cacheDir )->modify('fly_goods_spec',$upt_data,"spec_id='$id'",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}	
} //
?>