<?php
 /*
 *
 * admin.GoodsSku  商品SKU库   
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
class GoodsSku extends Action {

	private $cacheDir = ''; //缓存目录
	private $auth;
	public function __construct() {
		$this->auth = _instance( 'Action/sysmanage/Auth' );
	}
	public function goods_sku(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$goods_name	 = $this->_REQUEST("goods_name");
		$bdt   	   = $this->_REQUEST("bdt");
		$edt   	   = $this->_REQUEST("edt");
		$where_str = "goods_id != 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		if( !empty($goods_name) ){
			$where_str .=" and goods_name like '%$goods_name%'";
		}
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}	
		//排序生成
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_price' ){
			$order_by .=" price $orderDirection";
		}else if( $orderField=='by_stock' ){
			$order_by .=" stock $orderDirection";
		}else if( $orderField=='by_sort' ){
			$order_by .=" sort $orderDirection";
		}else if( $orderField=='by_stock' ){
			$order_by .=" stock $orderDirection";
		}else if( $orderField=='by_stock' ){
			$order_by .=" stock $orderDirection";
		}else{
			$order_by .=" goods_id desc";
		}
		
		//**************************************************************************
		$countSql	= "select * from fly_goods_sku where $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		
		$sql		= $countSql." $order_by limit $beginRecord,$pageSize";
		$list		= $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			//$list[$key]['member_name']	=$this->member->member_get_name($row['member_id']);
			//$list[$key]['shop_name']	=$this->shop->shop_get_name($row['shop_id']);
			//$list[$key]['goods_sku_list']=$this->goods_sku->goods_sku_list($row['goods_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
		
	}
	public function goods_sku_json(){
		$assArr  = $this->goods_sku();
		echo json_encode($assArr);
	}
	
	public function goods_sku_show(){
		$assArr  = $this->goods_sku();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('goods/goods_sku_show.html');	
	}
	public function goods_sku_add_save($goods_id,$datalist,$goods_name=null){
		//print_r($datalist);
		//统一删除商品所有图片
		//$this->C($this->cacheDir)->delete('fly_goods_sku',"goods_id='$goods_id'");
		if(!empty($datalist['sku_name'])){
			foreach($datalist['sku_name'] as $key=>$sku){

			//判断商品规则是否存在
			$sql	="select * from fly_goods_sku where goods_id='$goods_id' and sku_value_items='".$datalist['sku_value_items'][$key]."'";	
			$one	=$this->C($this->cacheDir)->findOne($sql);		
			$into_data=array(
				'goods_id'=>$goods_id,
				'goods_name'=>$goods_name,
				'sku_name'=>$datalist['sku_name'][$key],
				'sku_value_items'=>$datalist['sku_value_items'][$key],
				'market_price'=>$datalist['sku_market_price'][$key],
				'sale_price'=>$datalist['sku_sale_price'][$key],
				'cost_price'=>$datalist['sku_cost_price'][$key],
				'stock'=>$datalist['sku_stock'][$key],
				'create_date'=>NOWTIME,
				'update_date'=>NOWTIME,
			);				
			if(empty($one)){//不存在则添加
				$this->C($this->cacheDir)->insert('fly_goods_sku',$into_data);
			}else{//存在库里就更新
				$this->C($this->cacheDir)->modify('fly_goods_sku',$into_data,"goods_id='$goods_id' and sku_value_items='".$datalist['sku_value_items'][$key]."'");
			}

				
				//$sql = "insert into fly_goods_img(goods_id,img_path) values('$goods_id','$row')";
				//$this->C($this->cacheDir)->update($sql);
			}			
		}
	}
	//得到商品的所有sku
	public function goods_sku_list($id){
		if(empty($id)) $id=0;
		$sql	="select * from fly_goods_sku where goods_id='$id'";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
	
	//得到商品的所有sku
	public function goods_sku_get_one($id){
		if(empty($id)) $id=0;
		$sql	="select * from fly_goods_sku where goods_id='$id'";	
		$list	=$this->C($this->cacheDir)->findOne($sql);
		return $list;
	}
	
	//修改
	public function goods_sku_modify(){
		$sku_id	  	 = $this->_REQUEST("sku_id");
		if(empty($_POST)){
			$sql 	= "select * from fly_goods_sku where sku_id='$sku_id'";
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one));
			$smarty->display('goods/goods_sku_modify.html');	
		}else{//更新保存数据
			$sale_price	 = $this->_REQUEST("sale_price");
			$stock	  	 = $this->_REQUEST("stock");
			$upt_data=array(
				'sale_price'=>$this->_REQUEST("sale_price"),
				'market_price'=>$this->_REQUEST("market_price"),
				'update_date'=>NOWTIME
			);
			//修改商品数据
			$this->C($this->cacheDir)->modify('fly_goods_sku',$upt_data,"sku_id='$sku_id'");
			
			//计算商品SKU销售信息
			$this->goods_sku_modify_money($sku_id,$sale_price);
			
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}
	
	//更新计算金额
	public function goods_sku_modify_money($sku_id,$sale_price){
		//商品SKUK
		$sql="update fly_goods_sku set total_sale_money=sale_price*stock,total_profit_money=total_sale_money-total_cost_money where sku_id='$sku_id';";
		$this->C($this->cacheDir)->update($sql);
		//仓库商品
		$sql="update stock_goods_sku set sale_price='".$sale_price."', total_sale_money=(stock*".$sale_price."),total_profit_money=total_sale_money-total_cost_money where sku_id='$sku_id'";
		$one=$this->C($this->cacheDir)->update($sql);
		
	}

	//批量删除
	public function goods_sku_del(){
		$sku_id = $this->_REQUEST("sku_id");
		$sql	= "delete from fly_goods_sku where stock=0 and sku_id in ($sku_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");
	}


	
} //
?>