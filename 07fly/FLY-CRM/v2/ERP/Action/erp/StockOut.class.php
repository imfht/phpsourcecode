<?php
/*
 *
 * erp.StockOut  出库单
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
class StockOut extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
		$this->sys_user=_instance('Action/sysmanage/User');
		$this->store=_instance('Action/erp/StockStore');
		$this->contract=_instance('Action/crm/SalContract');
		$this->contract_list=_instance('Action/crm/SalContractList');
		$this->stock_goods_sku=_instance('Action/erp/StockGoodsSku');
	}	
	//库存清单商品SKU
	public function stock_out(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		$category_id= $this->_REQUEST("category_id");		
		$goods_name	= $this->_REQUEST("goods_name");		
		$sku_name	= $this->_REQUEST("sku_name");		
		$code		= $this->_REQUEST("code");		
		$where_str	= " s.out_id>0";
		
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
			$order_by .=" s.out_id desc";
		}		
		$countSql  = "select * from stock_out as s where $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql ="select s.* from stock_out as s where $where_str $order_by limit $beginRecord,$pageSize";
		$list=$this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['create_user_arr']	=$this->sys_user->user_get_one($row['create_user_id']);
			$list[$key]['out_user_arr']	=$this->sys_user->user_get_one($row['out_user_id']);
			$list[$key]['status_arr']	=$this->stock_out_status($row['status']);
			$list[$key]['store_arr']	=$this->store->stock_store_get_one($row['store_id']);
			//$list[$key]['goods_category']	=$this->goods_category->goods_category_get_one($row['category_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function stock_out_json(){
		$assArr  = $this->stock_out();
		echo json_encode($assArr);
	}
	public function stock_out_show(){
		$assArr  = $this->stock_out();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('erp/stock_out_show.html');	
	}
	
	//从销售单片直接生成出库单
	public function stock_out_contract_add(){
		
		$title		= $this->_REQUEST("title");
		$contract_id= $this->_REQUEST("contract_id");
		$store_id	= $this->_REQUEST("store_id");
		$contract_list_id= $this->_REQUEST("list_id");
		$sku_id		= $this->_REQUEST("sku_id");
		$sku_name	= $this->_REQUEST("sku_name");
		$goods_id	= $this->_REQUEST("goods_id");
		$goods_name	= $this->_REQUEST("goods_name");
		$cost_price	= $this->_REQUEST("cost_price");
		$num		= $this->_REQUEST("num");
		$out_num	= $this->_REQUEST("out_num");		
		$owe_num	= $this->_REQUEST("owe_num");		
		$owe_money	= $this->_REQUEST("owe_money");	
		$total_number	= array_sum($owe_num);
		$total_money	= array_sum($owe_money);

		if($total_number<=0){
			$this->L("Common")->ajax_json_error('本次出库数量合计不能小于0');
			return false;
		}
		
		foreach($sku_id as $ik=>$sku_one_id){
			$t_num=$out_num[$ik]+$owe_num[$ik];
			if($t_num>$num[$ik]){
				$this->L("Common")->ajax_json_error('本次出库数量不能大于销售数据');
				return false;
			}
		}
		
		//第一步生成出库音
		$out_data=array(
			'title'=>$title,
			'store_id'=>$store_id,
			'contract_id'=>$contract_id,
			'money'=>$total_money,
			'number'=>$total_number,
			'out_type'=>'销售出库',
			'create_time'=>NOWTIME,
			'create_user_id'=>SYS_USER_ID,
		);
		//插入记录，生成出库单
		$out_id=$this->C($this->cacheDir)->insert('stock_out',$out_data);
		if($out_id>0){
			//生成成出库明细，
			foreach($sku_id as $i=>$sku_one_id){
				//判断出库数据大于0166601275
				if($owe_num[$i]>0 && $owe_money[$i]>0){
					$out_data=array(
						'out_id'=>$out_id,
						'store_id'=>$store_id,
						'contract_id'=>$contract_id,
						'contract_list_id'=>$contract_list_id[$i],
						'sku_id'=>$sku_id[$i],
						'sku_name'=>$sku_name[$i],
						'goods_id'=>$goods_id[$i],
						'goods_name'=>$goods_name[$i],
						'price'=>$cost_price[$i],
						'number'=>$owe_num[$i],
						'money'=>$owe_money[$i],
						'create_time'=>NOWTIME,
						'create_user_id'=>SYS_USER_ID,
					);	
					$this->C($this->cacheDir)->insert('stock_out_list',$out_data);					
				}
			}
			//修改销售单为待出库
			$this->C($this->cacheDir)->modify('sal_contract',array('deliver_status'=>'3'),"contract_id='$contract_id'");
			//更新合同执行状态
			$this->contract->sal_contract_modify_status($contract_id);
			$this->L("Common")->ajax_json_success("出库单生成成功");
			
		}else{
			$this->L("Common")->ajax_json_error('出库单生成失败');
			return false;
		}
	}
	//确认出库
	public function stock_out_sure(){
		
		$this->C($this->cacheDir)->begintrans();
		
		$out_id = $this->_REQUEST("out_id");
		//更新库存清单，不存在就添加，以仓库，产品，SKU库存，的编号为标识
		$sql ="select * from stock_out_list where out_id='$out_id'";
		$list=$this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$contract_id		=$row['contract_id'];
			$contract_list_id	=$row['contract_list_id'];
			//更改库存清单 的数据
			$rtn_sku=$this->stock_goods_sku->stock_goods_sku_out_sure($row);
			if($rtn_sku['statusCode']=='300'){
				$this->C($this->cacheDir)->rollback();
				$this->L("Common")->ajax_json_error($rtn_sku['message']);
				exit;
			}
			//更改销售订单的数据
			$this->contract_list->sal_contract_list_stock_out_sure($contract_list_id,$row['number'],$row['money']);
		}

			
		//更新出库清单记录的出库人员
		$out_list_data=array('out_time'=>NOWTIME,'out_user_id'=>SYS_USER_ID,);	
		$this->C($this->cacheDir)->modify('stock_out_list',$out_list_data,"out_id='$out_id'");
		//修改销售单出库状态
		$this->contract->sal_contract_modify_deliver_status($contract_id);
		
		//事务提交
		$this->C($this->cacheDir)->commit();
		$this->L("Common")->ajax_json_success("出库成功");
	}
	
	//删除出库
	public function stock_out_del(){
		$out_id = $this->_REQUEST("out_id");
		$out_arr= explode(',',$out_id);
		//开启事务
		$this->C($this->cacheDir)->begintrans();
		foreach($out_arr as $one_id){
			$out_sql= "select * from stock_out where out_id='$one_id'";
			$out_one= $this->C($this->cacheDir)->findOne($out_sql);
			if($out_one['status']=='-1'){

				$this->C($this->cacheDir)->delete('stock_out',"out_id='$one_id'");
                //修改销售单为待出库
                $this->C($this->cacheDir)->modify('sal_contract',array('deliver_status'=>'2'),"contract_id='".$out_one['contract_id']."'");

			}else if($out_one['status']=='1'){
				$out_list_sql = "select * from stock_out_list where out_id='$one_id'";
				$out_list_list= $this->C($this->cacheDir)->findAll($out_list_sql);
				foreach($out_list_list as $row){
					$contract_id		=$row['contract_id'];
					$contract_list_id	=$row['contract_list_id'];
					//更改销售订单的数据
					$this->contract_list->sal_contract_list_stock_out_del($contract_list_id,$row['number'],$row['money']);
					//更改库存清单 的数据
					$this->stock_goods_sku->stock_goods_sku_out_del($row['sku_id'],$row['goods_id'],$row['store_id'],$row['number'],$row['money']);
				}
				//修改销售单出库状态
				$this->contract->sal_contract_modify_deliver_status($contract_id);
				//删除出库单和明细
				$this->C($this->cacheDir)->delete('stock_out',"out_id='$one_id'");
				$this->C($this->cacheDir)->delete('stock_out_list',"out_id='$one_id'");
			}
			
			//事务提交
			$this->C($this->cacheDir)->commit();
			
			$this->L("Common")->ajax_json_success("删除成功");
		}
	}
	//合同状态
	public function stock_out_status($key=null){
		$data=array(
			"-1"=>array(
				 		'status_name'=>'未出库',
				 		'status_name_html'=>'<span class="label label-warning">未出库<span>',
						'status_operation' => array(
                   '0' => array(
                        'act' => 'stock_sure',
                        'color' => '#7266BA',
                        'name' => '确认出库'
                    ),
							'1' => array(
                        'act' => 'delete',
                        'color' => '#F05050',
                        'name' => '删除'
                    ),
                ),
				),
			"1"=>array(
				 		'status_name'=>'已出库',
				 		'status_name_html'=>'<span class="label label-info">已出库<span>',
						'status_operation' => array(
                 	'0' => array(
                        'act' => 'delete',
                        'color' => '#F05050',
                        'name' => '删除'
                    )
                	),
					)
		);
		return ($key)?$data[$key]:$data;
	}	
}//
?>