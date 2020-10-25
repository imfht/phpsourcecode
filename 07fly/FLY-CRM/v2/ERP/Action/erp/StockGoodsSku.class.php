<?php
/*
 *
 * erp.StockStockGoodsSkuSku  库存商品清单
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
class StockGoodsSku extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
		$this->goods_category=_instance('Action/goods/GoodsCategory');
		$this->store=_instance('Action/erp/StockStore');
	}	
	//库存清单商品SKU
	public function stock_goods_sku(){
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
		$countSql  = "SELECT g.goods_name,s.* 
						FROM stock_goods_sku as s 
						LEFT JOIN fly_goods as g ON g.goods_id=s.goods_id 
						WHERE $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql ="SELECT g.goods_name,g.category_id,s.* 
				FROM stock_goods_sku as s 
				LEFT JOIN fly_goods as g ON g.goods_id=s.goods_id 
				WHERE $where_str $order_by LIMIT $beginRecord,$pageSize";
		$list=$this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['goods_category']	=$this->goods_category->goods_category_get_one($row['category_id']);
			$list[$key]['store']	=$this->store->stock_store_get_one($row['store_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function stock_goods_sku_json(){
		$assArr  = $this->stock_goods_sku();
		echo json_encode($assArr);
	}	
	public function stock_goods_sku_show(){
		$smarty = $this->setSmarty();
		$smarty->display('erp/stock_goods_sku_show.html');	
	}

	//当确认入库存单，更新库存清单
	public function stock_goods_sku_into_sure($row){
		$stock_goods_sku_sql="select * from stock_goods_sku 
							where store_id='".$row['store_id']."' 
							and goods_id='".$row['goods_id']."' 
							and sku_id='".$row['sku_id']."'";
		$stock_goods_sku_one=$this->C($this->cacheDir)->findOne($stock_goods_sku_sql);

		//判断是否存在库存清单 
		if(empty($stock_goods_sku_one)){
			$stock_goods_sku_data=array(
				'store_id'=>$row['store_id'],
				'sku_id'=>$row['sku_id'],
				'sku_name'=>$row['sku_name'],
				'goods_id'=>$row['goods_id'],
				'goods_name'=>$row['goods_name'],
				'cost_price'=>$row['price'],
				'stock'=>$row['number'],
				'total_cost_money'=>$row['money'],
				'create_time'=>NOWTIME,
			);
			$this->C($this->cacheDir)->insert('stock_goods_sku',$stock_goods_sku_data);
		}else{
			//当前库存成本金额/库存总数
			$stock			 =$stock_goods_sku_one['stock']+$row['number'];
			$total_cost_money=$stock_goods_sku_one['total_cost_money']+$row['money'];
			$cost_price		 =$total_cost_money/$stock;//重新计算成本价格
			
			$stock_goods_sku_data=array(
				'cost_price'		=>$cost_price,
				'stock'				=>$stock,
				'total_cost_money'	=>$total_cost_money,
				'update_time'		=>NOWTIME,
			);		
			$this->C($this->cacheDir)->modify('stock_goods_sku',$stock_goods_sku_data,"store_id='".$row['store_id']."' and goods_id='".$row['goods_id']."' and sku_id='".$row['sku_id']."'");	
		}	
		
		//同步商品SKU
		$this->goods_sku_modify_sync($row['goods_id'],$row['sku_id']);
	}
	
	//当删除入库存单，更新库存清单
	public function stock_goods_sku_into_del($sku_id,$goods_id,$store_id,$number,$money){
		$stock_goods_sku_sql="select * from stock_goods_sku  where store_id='$store_id' and goods_id='$goods_id'  and sku_id='$sku_id'";
		$stock_goods_sku_one=$this->C($this->cacheDir)->findOne($stock_goods_sku_sql);
		
		if($stock_goods_sku_one){
			$stock			 =$stock_goods_sku_one['stock']-$number;
			$total_cost_money=$stock_goods_sku_one['total_cost_money']-$money;
			$cost_price		 =($total_cost_money==0)?0:$total_cost_money/$stock;//重新计算成本价格
			$stock_goods_sku_data=array(
				'cost_price'		=>$cost_price,
				'stock'				=>$stock,
				'total_cost_money'	=>$total_cost_money,
				'update_time'		=>NOWTIME,
			);		
			$this->C($this->cacheDir)->modify('stock_goods_sku',$stock_goods_sku_data,"store_id='$store_id' and goods_id='$goods_id' and sku_id='$sku_id'");
			//同步商品SKU
			$this->goods_sku_modify_sync($goods_id,$sku_id);
		}
	}

	//当删除出库存单，更新库存清单
	public function stock_goods_sku_out_del($sku_id,$goods_id,$store_id,$number,$money){
		$stock_goods_sku_sql="select * from stock_goods_sku  where store_id='$store_id' and goods_id='$goods_id'  and sku_id='$sku_id'";
		$stock_goods_sku_one=$this->C($this->cacheDir)->findOne($stock_goods_sku_sql);
		if($stock_goods_sku_one){
			$stock			 =$stock_goods_sku_one['stock']+$number;
			$total_cost_money=$stock_goods_sku_one['total_cost_money']+$money;
			$cost_price		 =($total_cost_money==0)?0:$total_cost_money/$stock;//重新计算成本价格
			$stock_goods_sku_data=array(
				'cost_price'		=>$cost_price,
				'stock'				=>$stock,
				'total_cost_money'	=>$total_cost_money,
				'update_time'		=>NOWTIME,
			);		
			$this->C($this->cacheDir)->modify('stock_goods_sku',$stock_goods_sku_data,"store_id='$store_id' and goods_id='$goods_id' and sku_id='$sku_id'");
			//同步商品SKU
			$this->goods_sku_modify_sync($goods_id,$sku_id);
		}
	}
	
	//当确认出库单，更新库存清单
	public function stock_goods_sku_out_sure($row){
		$stock_goods_sku_sql="SELECT * from stock_goods_sku 
							WHERE store_id='".$row['store_id']."' 
							and goods_id='".$row['goods_id']."' 
							and sku_id='".$row['sku_id']."'";
		$stock_goods_sku_one=$this->C($this->cacheDir)->findOne($stock_goods_sku_sql);

		//判断是否存在库存清单 
		if(empty($stock_goods_sku_one)){
			$message="商品编号:".$row['goods_id'].",SKU编号".$row['sku_id'].",名称".$row['goods_name']." 没有库存";
			return array('statusCode'=>'300','message'=>$message);
			exit;
		}else{
			$stock =$stock_goods_sku_one['stock']-$row['number'];
			if($stock>=0){
				$total_cost_money=$stock_goods_sku_one['total_cost_money']-$row['money'];
				$cost_price=($total_cost_money==0)?0:$total_cost_money/$stock;//重新计算成本价格
				$stock_goods_sku_data=array(
					'cost_price'		=>$cost_price,
					'stock'				=>$stock,
					'total_cost_money'	=>$total_cost_money,
					'update_time'		=>NOWTIME,
				);		
				$this->C($this->cacheDir)->modify('stock_goods_sku',$stock_goods_sku_data,"store_id='".$row['store_id']."' and goods_id='".$row['goods_id']."' and sku_id='".$row['sku_id']."'");				////同步商品SKU
				$this->goods_sku_modify_sync($row['goods_id'],$row['sku_id']);	
			}else{
				$message="商品编号:".$row['goods_id'].",SKU编号".$row['sku_id'].",名称".$row['goods_name']." 库存不足";
				return array('statusCode'=>'300','message'=>$message);
			}
		}
	
		return array('statusCode'=>'200','message'=>"库存清单更新完成");
	}
	
	//同步商品SKU的库存信息和成本
	//第一步：为把仓库的清单库存数据同步到商品SKU中
	//第二步：把SKU的价格同步到仓库中来
	public function goods_sku_modify_sync($goods_id,$sku_id){
		//第一步、查询sku_id所有数
		$sql="SELECT SUM(stock) as t_stock,SUM(total_cost_money) as t_cost_money FROM stock_goods_sku WHERE  goods_id='$goods_id' and sku_id='$sku_id'";
		$one=$this->C($this->cacheDir)->findOne($sql);
		$cost_price=$one['t_cost_money']/$one['t_stock'];
		
		//更改商品sku库存，成本
		$updata=array(
			'stock'=>$one['t_stock'],
			'total_cost_money'=>$one['t_cost_money'],
			'cost_price'=>$cost_price,
		);
		$this->C($this->cacheDir)->modify('fly_goods_sku',$updata,"goods_id='$goods_id' and sku_id='$sku_id'");	
		
		//查商品销售价格
		$sql="select * from fly_goods_sku where sku_id='$sku_id'";
		$one=$this->C($this->cacheDir)->findOne($sql);
		$sale_price=$one['sale_price'];
		$sql="update stock_goods_sku set sale_price='".$sale_price."', total_sale_money=(stock*".$sale_price."),total_profit_money=total_sale_money-total_cost_money where sku_id='$sku_id'";
		$one=$this->C($this->cacheDir)->update($sql);
	}

}//
?>