<?php
 /*
 *
 * admin.GoodsAttrValue  商品属性值管理   
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
class GoodsAttrValue extends Action {

	private $cacheDir = ''; //缓存目录
	private $auth;
	public function __construct() {
		$this->auth = _instance( 'Action/sysmanage/Auth' );
	}

	public function goods_attr_value() {
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
		$countSql    = "select * from fly_goods_attr_value where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql	= "select * from fly_goods_attr_value  where $where_str $order_by limit $beginRecord,$pageSize";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function goods_attr_value_json() {
		$list	=$this->goods_attr_value();
		echo json_encode($list);
	}
	public
	function goods_attr_value_show() {
		$list =$this->goods_attr_value();
		$smarty = $this->setSmarty();
		$smarty->assign(array( "list" => $list) );
		$smarty->display('goods/goods_attr_value_show.html');
	}

	public function goods_attr_value_add($data) {
		if ( !empty( $data ) ) {
			$this->goods_attr_value_del($data['attr_id']);
			for($i=0;$i<count($data['attr_value_name']);$i++){
				$savedata=array(
					'attr_id'=>$data['attr_id'],
					'attr_value_name'=>$data['attr_value_name'][$i],
					'attr_value_data'=>$data['attr_value_data'][$i],
					'type'=>$data['attr_value_type'][$i],
					'sort'=>$data['attr_value_sort'][$i],
					'is_search'=>$data['attr_value_is_search'][$i],
				 );
				$this->C( $this->cacheDir )->insert('fly_goods_attr_value',$savedata );
			}
			
		}
	}
	
	public
	function goods_attr_value_del($id) {
		if(empty($id)) $id=0;
		$this->C( $this->cacheDir )->delete('fly_goods_attr_value',"attr_id='$id'");
		return true;
	}

	//得到所有属性值名称
	public function goods_attr_value_list($id){
		if(empty($id)) $id=0;
		$sql	="select * from fly_goods_attr_value where attr_id='$id' order by sort asc";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
	
	//得到所有属性值名称
	public function goods_attr_value_name($id){
		if(empty($id)) $id=0;
		$sql  ="select attr_value_name from fly_goods_attr_value where attr_id='$id' order by sort asc";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "【".$row["attr_value_name"]."】 ";
			}
		}
		return $str;
	}


} //
?>