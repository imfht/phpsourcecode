<?php
/*
 *
 * erp.StockOutList 出库清单
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
class StockOutList extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
		$this->goods_category=_instance('Action/goods/GoodsCategory');
		$this->store=_instance('Action/erp/StockStore');
		$this->stock_out=_instance('Action/erp/StockOut');
	}	
	//库存清单商品SKU
	public function stock_out_list(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		$category_id= $this->_REQUEST("category_id");		
		$goods_name	= $this->_REQUEST("goods_name");		
		$sku_name	= $this->_REQUEST("sku_name");		
		$code		= $this->_REQUEST("code");		
		$where_str	= " s.sku_id>0";
		
		//获得分类及子类的商品条件
		if(!empty($category_id)){
			$child_arr=$this->goods_category->goods_category_all_child($category_id);
			if(empty($child_arr)){
				$child_txt="$category_id";
			}else{
				$child_txt=implode(',',$child_arr).",$category_id";
			}
			$where_str	.= " and g.category_id in ($child_txt)";
		}
		if(!empty($goods_name)){
			$where_str	.= " and g.goods_name like '%$goods_name%'";
		}
		if(!empty($sku_name)){
			$where_str	.= " and s.sku_name like '%$sku_name%'";
		}
		if(!empty($code)){
			$where_str	.= " and s.code like '%$code%'";
		}
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");		
		$order_by="order by";
		if( $orderField=='by_saleprice' ){
			$order_by .=" s.sale_price $orderDirection";
		}else if($orderField=='by_marketprice'){
			$order_by .=" s.market_price $orderDirection";
		}else if($orderField=='by_costprice'){
			$order_by .=" s.cost_price $orderDirection";
		}else if($orderField=='by_stock'){
			$order_by .=" s.stock $orderDirection";
		}else{
			$order_by .=" s.sku_id desc";
		}		
		$countSql  = "select g.goods_name,s.* from stock_out_list as s 
						left join fly_goods as g on g.goods_id=s.goods_id 
						left join stock_out as i on i.out_id=s.out_id 
						where $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql ="select g.goods_name,g.category_id,s.*,i.title from stock_out_list as s 
				left join fly_goods as g on g.goods_id=s.goods_id 
				left join stock_out as i on i.out_id=s.out_id
				where $where_str $order_by limit $beginRecord,$pageSize";
		$list=$this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['goods_category']	=$this->goods_category->goods_category_get_one($row['category_id']);
			$list[$key]['store']	=$this->store->stock_store_get_one($row['store_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function stock_out_list_json(){
		$assArr  = $this->stock_out_list();
		echo json_encode($assArr);
	}	
	public function stock_out_list_show(){
		$assArr  		= $this->stock_out_list();
		$smarty  		= $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('erp/stock_out_list_show.html');	
	}

	
}//
?>