<?php
/*
 * 产品管理类
 */	
class WxGoodsBuy extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
		$this->goods_cart=_instance('Action/home/WxGoodsCart');
		$this->comm=_instance('Extend/Common');
		
	}
	//产品购买
	public function goods_buy(){
		$id	 	= $this->_REQUEST("id");
		$action	= $this->_REQUEST("action");
		if(empty($_POST)){
			$this->goods_cart->goods_card_add($id);//先加放购买车
			$list=$this->goods_cart->goods_card_list();
			$address=$this->L('home/WxMemberAddress')->member_address_get_default();
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("list"=>$list,"address"=>$address));
			$smarty->display('home/goods_buy.html');				
		}else{			
			$action	= $this->_REQUEST("action");
			$all_money	= $this->_REQUEST("all_money");
			$shiping_money	= $this->_REQUEST("shiping_money");
			$address_id		= $this->_REQUEST("address_id");
			
			$shop_id	= $this->_REQUEST("shop_id");
			$goods_id	= $this->_REQUEST("goods_id");
			$goods_name	= $this->_REQUEST("goods_name");
			$price		= $this->_REQUEST("price");
			$money		= $this->_REQUEST("money");
			$number		= $this->_REQUEST("number");
			if($action=='save'){
				$buy_member=$this->L('home/WxMember')->member_get_info();
				$buyer_member_id=$buy_member['member_id'];
				$into_data=array(
					'adt'=>NOWTIME,
					'goods_money'=>$all_money,
					'order_money'=>$all_money+$shiping_money,
					'pay_money'=>$all_money+$shiping_money,
					'shipping_money'=>$shiping_money,
					'address_id'=>$shiping_money,
					'buyer_member_id'=>$buyer_member_id,
					'receiver_name'=>$this->_REQUEST("address_name"),
					'receiver_mobile'=>$this->_REQUEST("address_mobile"),
					'receiver_address'=>$this->_REQUEST("address_address"),
				);	
				$order_id=$this->C($this->cacheDir)->insert('fly_goods_order',$into_data);
				if($order_id){
					$order_no=date('Ymd',time()).'-'.$order_id;
					$upt_data=array('order_no'=>$order_no);
					$this->C($this->cacheDir)->modify('fly_goods_order',$upt_data,"order_id='$order_id'");
					foreach($goods_id as $i=>$id){
						$into_data=array(
							'order_id'=>$order_id,
							'goods_id'=>$id,
							'goods_name'=>$goods_name[$i],
							'sale_price'=>$price[$i],
							'num'=>$number[$i],
							'goods_money'=>$price[$i]*$number[$i],	
							'buyer_member_id'=>$buyer_member_id,
						);					
						$this->C($this->cacheDir)->insert('fly_goods_order_list',$into_data);
					}					
				}
				echo $this->comm->ajax_json_success($order_id);
			}
		}	
	}
	
	//支付订单金额
	public function goods_buy_pay(){
		$order_id = $this->_REQUEST("order_id");
		if(empty($_POST)){
			$sql   = "select * from fly_goods_order where order_id='$order_id'";
			$one   = $this->C($this->cacheDir)->findOne($sql);
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one));
			$smarty->display('home/goods_buy_pay.html');				
		}else{
			$member		  	=$this->L('home/WxMember')->member_get_info();
			$buyer_member_id=$member['member_id'];
			$buyer_member_balance=$member['balance'];
			$pay_mode	 	= $this->_REQUEST("pay_mode");
			
			if($pay_mode=='1'){//表示帐户余额支付
				$sql = "select * from fly_goods_order where ifpay='0' and order_id='$order_id'";
				$one = $this->C($this->cacheDir)->findOne($sql);
				if(!empty($one)){
					$money	= $one['goods_money'];
					if($money<=$buyer_member_balance){//余额大于订单总金额
						//更改订单为支付状态
						$sql="update fly_goods_order set ifpay='1',status='1' where order_id='$order_id'";
						$this->C($this->cacheDir)->update($sql);
						
						//扣出买家余额
						$sql="update fly_member set balance=balance-$money where member_id='$buyer_member_id'";
						$this->C($this->cacheDir)->update($sql);
						
						//针对购买会员对上级进行返点增加收入
						$this->goods_buy_pay_quick($buyer_member_id,$money,$order_id);
						$rtn_msg=array('statusCode'=>'200','message'=>'支付成功');	
					}else{
						$rtn_msg=array('statusCode'=>'201','message'=>'帐户余额不足');	
					}
					
				}else{
					$rtn_msg=array('statusCode'=>'201','message'=>'订单有错误');
				}
			}else{
				$rtn_msg=array('statusCode'=>'201','message'=>'支付类型出错');
			}
			echo json_encode($rtn_msg);
		}
	}
	
	//支付金额给生佣金订单
	public function goods_buy_pay_quick($buyer_member_id,$money,$goods_order_id){
		//查询买家祖节点
		$ancestor_arr  =$this->L('admin/MemberTree')->get_member_ancestor($buyer_member_id);
		$ancestor_txt	=implode(',',$ancestor_arr);
		//查询卖家信息
		//$member_sell  =$this->L('home/WxMember')->member_get_one($seller_member_id);

		$sql="insert into fly_goods_order_quick(goods_order_id,buyer_member_id,money,buyer_ancestor_id,adt) 
						values('$goods_order_id','$buyer_member_id','$money','$ancestor_txt','".NOWTIME."')";
		$rtn=$this->C($this->cacheDir)->update($sql);
		if($rtn>0){
			$this->goods_buy_pay_quick_run($rtn);//关联释放
			return true;
		}else{
			return false;
		}
	}
	
	//增返点订单逐个返金额
	public function goods_buy_pay_quick_run($out_id=1){
			$o_sql="select * from fly_goods_order_quick where id='$out_id'";
			$o_one= $this->C($this->cacheDir)->findOne($o_sql);
			$money		=$o_one['money'];//交易金额
			$goods_order_id =$o_one['goods_order_id'];//订单编号 
			$buyer_member_id	=$o_one['buyer_member_id'];//购买人员编号 
			$seller_member_id	=$o_one['seller_member_id'];//销售人员编号
			$buyer_ancestor_id=$o_one['buyer_ancestor_id'];//购买人员上一级
		   $ancestor_out_arr=explode(',',$buyer_ancestor_id);//购买会员的上级	
			//查祖节点加速
			foreach($ancestor_out_arr as $key=>$ancestor_id){
				$layers=$key;
				//echo "查询祖级数据：".$m_sql."<hr>";
				$m_sql="select * from fly_member where member_id='$ancestor_id'";
				$m_one= $this->C($this->cacheDir)->findOne($m_sql);
				$member_type_id=$m_one['member_type_id'];//会员所在组

				//查询被加速会员的所在组和层数的返点比例
				$qk_sql="select rate from fly_member_type_dist where member_type_id='$member_type_id'  and layers='$layers'";
				//echo "=>$key =查询祖级比例：".$qk_sql."<hr>";
				$qk_one=$this->C($this->cacheDir)->findOne($qk_sql);
				
				if(!empty($qk_one)){
					$money_dist =$money*$qk_one['rate'];
					//echo "祖级返点金额：".$qk_integral;
					$sql="insert into fly_member_quick(goods_order_id,money,money_dist,
														quick_member_id,member_layers,member_rate,buyer_member_id,intro,adt)
											values('$goods_order_id','$money','$money_dist',
														'$ancestor_id','$layers','$qk_one[rate]','$buyer_member_id','佣金:下级返点','".NOWTIME."')";
					$rtn=$this->C($this->cacheDir)->update($sql);
				}
				//echo "<hr>";
			}//祖节点加速结束
		return true;
	}
}//
?>