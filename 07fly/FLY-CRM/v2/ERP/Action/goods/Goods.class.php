<?php
/*
 *
 * goods.Goods  商品管理   
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
class Goods extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
		$this->goods_gategory=_instance('Action/goods/GoodsCategory');
		$this->goods_img=_instance('Action/goods/GoodsImg');
		$this->goods_sku=_instance('Action/goods/GoodsSku');
	}	
	public function goods(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
        $keywords	   = $this->_REQUEST("keywords");
		$tel	   = $this->_REQUEST("tel");
		$trade     = $this->_REQUEST("org2_id");
		$fax   	   = $this->_REQUEST("fax");
		$email     = $this->_REQUEST("email");
		$address   = $this->_REQUEST("address");	
		$bdt   	   = $this->_REQUEST("bdt");
		$edt   	   = $this->_REQUEST("edt");
		$where_str = "goods_id != 0";

		if( !empty($keywords) ){
			$where_str .=" and (goods_name like '%$keywords%' or  sku_name like '%$keywords%')";
		}
		if( !empty($tel) ){
			$where_str .=" and tel like '%$tel%'";
		}	
		if( !empty($trade) ){
			$where_str .=" and trade ='$trade'";
		}	
		if( !empty($fax) ){
			$where_str .=" and fax like '%$fax%'";
		}	
		if( !empty($email) ){
			$where_str .=" and email like '%$email%'";
		}	
		if( !empty($address) ){
			$where_str .=" and address like '%$address%'";
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
		$countSql	= "select goods_id from fly_goods where $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		= "select * from fly_goods  where $where_str $order_by limit $beginRecord,$pageSize";
		$list		= $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			//$list[$key]['member_name']	=$this->member->member_get_name($row['member_id']);
			//$list[$key]['shop_name']	=$this->shop->shop_get_name($row['shop_id']);
			$list[$key]['goods_sku_list']=$this->goods_sku->goods_sku_list($row['goods_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
		
	}
	public function goods_json(){
		$assArr  = $this->goods();
		echo json_encode($assArr);
	}
	
	public function goods_show(){
		$assArr  = $this->goods();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('goods/goods_show.html');	
	}

	public function goods_show_one(){
		$id	 = $this->_REQUEST("id");
		$sql = "select * from fly_goods where id='$id'";
		$one = $this->C($this->cacheDir)->findOne($sql);	
		$one['members_name']=$this->member->member_get_name($one['members']);
		$one['type_name']=$this->type->goods_type_get_name($one['type_id']);
		$one['status_name']=$this->goods_status($one['status']);
		$smarty 	= $this->setSmarty();
		$smarty->assign(array("one"=>$one));
		$smarty->display('goods/goods_show_one.html');	
	}

	public function goods_add(){
		if(empty($_POST)){
			$goods_category_select=$this->goods_gategory->goods_category_select('category_id');
			$smarty = $this->setSmarty();
			$smarty->assign(array("goods_category_select"=>$goods_category_select));
			$smarty->display('goods/goods_add.html');	
		}else{
			if($this->goods_add_save()){
				$this->location("操作成功","/goods/Goods/goods_show/");
			}
				
		}
	}
	//数据保存
	public function goods_add_save(){
		$nowtime	=date('Y-m-d H:i:s',time());
		$imglistname=$this->_REQUEST("imglistname");
		$postdata=array(
			'goods_name'=>$this->_REQUEST("goods_name"),
			'category_id'=>$this->_REQUEST("category_id"),
			'brand_id'=>$this->_REQUEST("brand_id"),
			'keywords'=>$this->_REQUEST("keywords"),
			'description'=>$this->_REQUEST("description"),
			'content'=>$this->_REQUEST("content"),
			'market_price'=>$this->_REQUEST("market_price"),
			'sale_price'=>$this->_REQUEST("sale_price"),
			'cost_price'=>$this->_REQUEST("cost_price"),
			'sort'=>$this->_REQUEST("sort"),
			'stock'=>$this->_REQUEST("stock"),
			'defaultpic'=>!empty($imglistname)?$imglistname[0]:"",
			'state'=>$this->_REQUEST("state"),
			'create_time'=>NOWTIME,
		);
		$goods_id=$this->C($this->cacheDir)->insert('fly_goods',$postdata);
		$goods_name=$this->_REQUEST("goods_name");
		$sku_name=$this->_REQUEST("sku_name");
		if($goods_id>0){
			//判断是否使用规格
			if(!empty($sku_name)){
				$datalist=array(
					'sku_name'=>$this->_REQUEST("sku_name"),
					'sku_value_items'=>$this->_REQUEST("sku_value_items"),
					'sku_sale_price'=>$this->_REQUEST("sku_sale_price"),
					'sku_market_price'=>$this->_REQUEST("sku_market_price"),
					'sku_cost_price'=>$this->_REQUEST("sku_cost_price"),
					'sku_stock'=>$this->_REQUEST("sku_stock"),
				);				
			}else{
				$datalist=array(
					'sku_name'=>array($this->_REQUEST("goods_name")),
					'sku_value_items'=>array($goods_id),
					'sku_sale_price'=>array($this->_REQUEST("sale_price")),
					'sku_market_price'=>array($this->_REQUEST("market_price")),
					'sku_cost_price'=>array($this->_REQUEST("cost_price")),
					'sku_stock'=>array($this->_REQUEST("stock")),
				);					
			}

			$this->goods_img->goods_img_add_save($goods_id,$imglistname);
			$this->goods_sku->goods_sku_add_save($goods_id,$datalist,$goods_name);
			return true;
		}else{
			return false;
		}
	}
	//修改
	public function goods_modify(){
		$goods_id	  	 = $this->_REQUEST("goods_id");
		if(empty($_POST)){
			$sql = "select * from fly_goods where goods_id='$goods_id'";
			$one = $this->C($this->cacheDir)->findOne($sql);
			$goods_category_select=$this->goods_gategory->goods_category_select('category_id',$one['category_id']);
			$one['goods_sku_list']=$this->goods_sku->goods_sku_list($goods_id);
			$one['goods_img_list']=$this->goods_img->goods_img_list($goods_id);
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,'goods_category_select'=>$goods_category_select));
			$smarty->display('goods/goods_modify.html');	
		}else{//更新保存数据
			$imglistname=$this->_REQUEST("imglistname");
			$goods_name=$this->_REQUEST("goods_name");
			$sku_name=$this->_REQUEST("sku_name");
			$upt_data=array(
				'goods_name'=>$this->_REQUEST("goods_name"),
				'category_id'=>$this->_REQUEST("category_id"),
				'keywords'=>$this->_REQUEST("keywords"),
				'description'=>$this->_REQUEST("description"),
				'content'=>$this->_REQUEST("content"),
				'market_price'=>$this->_REQUEST("market_price"),
				'sale_price'=>$this->_REQUEST("sale_price"),
				'cost_price'=>$this->_REQUEST("cost_price"),
				'stock'=>$this->_REQUEST("stock"),
				'sort'=>$this->_REQUEST("sort"),
				'defaultpic'=>!empty($imglistname)?$imglistname[0]:"",
				'state'=>$this->_REQUEST("state"),
				'update_time'=>NOWTIME
			);
			//修改商品数据
			$this->C($this->cacheDir)->modify('fly_goods',$upt_data,"goods_id='$goods_id'");	
			//判断是否使用规格
			if(!empty($sku_name)){
				$datalist=array(
					'sku_name'=>$this->_REQUEST("sku_name"),
					'sku_value_items'=>$this->_REQUEST("sku_value_items"),
					'sku_sale_price'=>$this->_REQUEST("sku_sale_price"),
					'sku_market_price'=>$this->_REQUEST("sku_market_price"),
					'sku_cost_price'=>$this->_REQUEST("sku_cost_price"),
					'sku_stock'=>$this->_REQUEST("sku_stock"),
					'update_time'=>NOWTIME
				);				
			}else{
				$datalist=array(
					'sku_name'=>array($this->_REQUEST("goods_name")),
					'sku_value_items'=>array($this->_REQUEST("goods_name")),
					'sku_sale_price'=>array($this->_REQUEST("sale_price")),
					'sku_market_price'=>array($this->_REQUEST("market_price")),
					'sku_cost_price'=>array($this->_REQUEST("cost_price")),
					'sku_stock'=>array($this->_REQUEST("stock")),
					'update_time'=>NOWTIME
				);					
			}
			$this->goods_img->goods_img_add_save($goods_id,$imglistname);
			$this->goods_sku->goods_sku_add_save($goods_id,$datalist,$goods_name);
			$this->location("操作成功","/goods/Goods/goods_show/");		
		}
	}
	//排序
	public function goods_modify_sort() {
		$goods_id=$this->_REQUEST('goods_id');	
		$sort	=$this->_REQUEST('sort');	
		$upt_data=array(
					'sort'=>$this->_REQUEST( "sort" )
				 );
		$this->C( $this->cacheDir )->modify('fly_goods',$upt_data,"goods_id='$goods_id'",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}	
	public function goods_status(){
		$rtn=array(
			"0"=>"待审核",
			"1"=>"已审核"
		);
		return $rtn;
	}
	//批量删除
	public function goods_del(){
		$goods_id = $this->_REQUEST("goods_id");
		$sql	= "delete from fly_goods where goods_id in ($goods_id)";
		$this->C($this->cacheDir)->update($sql);	
		$rtnArr=array('rtnstatus'=>'success','msg'=>'删除成功');
		echo json_encode($rtnArr);
	}
	
	//上架
	public function goods_modify_online() {
		$goods_id=$this->_REQUEST('goods_id');	
		$upt_data=array(
					'state'=>'1'
				 );
		$this->C( $this->cacheDir )->modify('fly_goods',$upt_data,"goods_id in($goods_id)",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}
	//下架
	public function goods_modify_offline() {
		$goods_id=$this->_REQUEST('goods_id');	
		$upt_data=array(
					'state'=>'0'
				 );
		$this->C( $this->cacheDir )->modify('fly_goods',$upt_data,"goods_id in($goods_id)",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}	
	//推荐
	public function goods_modify_recommend() {
		$goods_id=$this->_REQUEST('goods_id');	
		$upt_data=array(
					'is_recommend'=>'1'
				 );
		$this->C( $this->cacheDir )->modify('fly_goods',$upt_data,"goods_id in($goods_id)",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}
	//取消推荐
	public function goods_modify_recommend_off() {
		$goods_id=$this->_REQUEST('goods_id');	
		$upt_data=array(
					'is_recommend'=>'0'
				 );
		$this->C( $this->cacheDir )->modify('fly_goods',$upt_data,"goods_id in($goods_id)",true);
		$rtnArr=array('rtnstatus'=>'success','msg'=>'');
		echo json_encode($rtnArr);
	}	
	//传入ID返回名字
	public function goods_get_one($id){
		if(empty($id)) $id=0;
		$sql ="select * from fly_goods where goods_id in ($id)";	
		$one =$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}

	//通过树形选择查询，商品SKU
	public function goods_list_tree(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		$category_id= $this->_REQUEST("category_id");		
		$keywords	= $this->_REQUEST("keywords");
		$sku_name	= $this->_REQUEST("sku_name");		
		$code		= $this->_REQUEST("code");		
		$where_str	= " s.sku_id>0";
		
		//获得分类及子类的商品条件
		if(!empty($category_id)){
			$child_arr=$this->goods_gategory->goods_category_all_child($category_id);
			if(empty($child_arr)){
				$child_txt="$category_id";
			}else{
				$child_txt=implode(',',$child_arr).",$category_id";
			}
			$where_str	.= " and g.category_id in ($child_txt)";
		}
        if( !empty($keywords) ){
            $where_str .=" and (s.goods_name like '%$keywords%' or  s.sku_name like '%$keywords%')";
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
		$countSql  = "select g.goods_name,s.* from fly_goods_sku as s left join fly_goods as g on g.goods_id=s.goods_id where $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql ="select g.goods_name,s.* from fly_goods_sku as s left join fly_goods as g on g.goods_id=s.goods_id 
				where $where_str $order_by limit $beginRecord,$pageSize";
		$list=$this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		echo json_encode($assignArray);		
	}
	
	function goods_list_loop(){
		$smarty = $this->setSmarty();
		$smarty->assign(array());
		$smarty->display('goods/goods_list_loop.html');		
	}	

	function goods_list_loop_pos(){
		$smarty = $this->setSmarty();
		$smarty->assign(array());
		$smarty->display('goods/goods_list_loop_pos.html');		
	}
	
}//
?>