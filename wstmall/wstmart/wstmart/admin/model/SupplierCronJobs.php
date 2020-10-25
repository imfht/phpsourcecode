<?php
namespace wstmart\admin\model;
use think\Db;
use Env;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 定时业务处理
 */
class SupplierCronJobs extends Base{
	/**
	 * 管理员登录触发动作
	 */
	public function autoByAdmin(){
		$this->autoCancelNoPay();
		$this->autoReceive();
		$this->autoAppraise();
		$this->autoSupplierSettlement();
	}
	/**
	 * 取消未支付订单
	 */
	public function autoCancelNoPay(){
		$autoCancelNoPayDays = (int)WSTConf('CONF.autoCancelNoPayDays');
	 	$autoCancelNoPayDays = ($autoCancelNoPayDays>0)?$autoCancelNoPayDays:6;
	 	$lastDay = date("Y-m-d H:i:s",strtotime("-".$autoCancelNoPayDays." hours"));
	 	$orders = Db::name('supplier_orders')->alias('o')->join('suppliers s','o.supplierId=s.supplierId','left')->where([['o.createTime','<',$lastDay],['o.orderStatus','=',-2],['o.dataFlag','=',1],['o.payType','=',1],['o.isPay','=',0]])->field("o.orderId,o.orderNo,o.userId,o.supplierId,s.userId supplierUserId,orderCode")->select();
	 	if(!empty($orders)){
	 		$prefix = config('database.prefix');
	 		$orderIds = [];
	 		foreach ($orders as $okey => $order){
	 			$orderIds[] = $order['orderId'];
	 		}
	 		Db::startTrans();
		    try{
		    	//提前锁定订单
		    	Db::name('supplier_orders')->where([['orderId','in',$orderIds]])->update(['orderStatus'=>-1,'realTotalMoney'=>0]);
                foreach ($orders as $okey => $order){
                	$supplierId = $order['supplierId'];
                	$goods = Db::name('supplier_order_goods')->alias('og')->join('supplier_goods g','og.goodsId=g.goodsId','inner')
					           ->where('orderId',$order['orderId'])->field('og.*,g.isSpec')->select();
					foreach ($goods as $k => $v){
						
				    	//只有正常下单的才会修改库存的，其他的任何插件都不会修改库存
				    	if($order['orderCode'] == 'order'){
							//修改库存
							if($v['isSpec']>0){
						        Db::name('supplier_goods_specs')->where('id',$v['goodsSpecId'])->setInc('specStock',$v['goodsNum']);
							}
							Db::name('supplier_goods')->where('goodsId',$v['goodsId'])->setInc('goodsStock',$v['goodsNum']);
				        }
					    
					}
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $order['orderId'];
					$logOrder['orderStatus'] = -1;
					$logOrder['logContent'] = "订单长时间未支付，系统自动取消订单";
					$logOrder['logUserId'] = $order['userId'];
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('supplier_log_orders')->insert($logOrder);
                    //发送消息
	                $tpl = WSTMsgTemplates('ORDER_USER_PAY_TIMEOUT');
	                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                    $find = ['${ORDER_NO}'];
	                    $replace = [$order['orderNo']];
	                    //发送一条用户信息
					    WSTSendMsg($order['userId'],str_replace($find,$replace,$tpl['tplContent']),['from'=>1,'dataId'=>$order['orderId']]);
	                }
                    $tpl = WSTMsgTemplates('ORDER_SUPPLIER_PAY_TIMEOUT');
	                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                    $find = ['${ORDER_NO}'];
	                    $replace = [$order['orderNo']];
	                    //发送一条商家信息
					    
	                	$msg = array();
			            $msg["supplierId"] = $supplierId;
			            $msg["tplCode"] = $tpl["tplCode"];
			            $msg["msgType"] = 1;
			            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']) ;
			            $msg["msgJson"] = ['from'=>1,'dataId'=>$order['orderId']];
			            model("common/SupplierMessageQueues")->add($msg);
	                }
                }

		        Db::commit();
				return WSTReturn('操作成功',1);
	 		}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
	 	}
	 	return WSTReturn('操作成功',1);
	}
    /**
	 * 自动好评
	 */
	public function autoAppraise(){
        $autoAppraiseDays = (int)WSTConf('CONF.autoAppraiseDays');
	 	$autoAppraiseDays = ($autoAppraiseDays>0)?$autoAppraiseDays:7;//避免有些客户没有设置值
	 	$lastDay = date("Y-m-d 00:00:00",strtotime("-".$autoAppraiseDays." days"));
	 	$rs = model('supplier/SupplierOrders')->where([['receiveTime','<',$lastDay],['orderStatus','=',2],['dataFlag','=',1],['isAppraise','=',0]])->field("orderId,userId,supplierId,orderNo")->select();
	 	if(!empty($rs)){
	 		$prefix = config('database.prefix');
	 		$orderIds = [];
	 		foreach ($rs as $okey => $order){
	 			$orderIds[] = $order->orderId;
	 		}
	 		Db::startTrans();
		    try{
		    	//提前锁定订单
		    	Db::name('supplier_orders')->where([['orderId','in',$orderIds]])->update(['isAppraise'=>1]);
		    	foreach ($rs as $okey => $order){;
		    	    //获取订单相关的商品
		    	    $ordergoods = Db::name('supplier_order_goods')->where('orderId',$order->orderId)->field('id,goodsId,orderId,goodsSpecId')->select();
		    	    foreach($ordergoods as $goods){
						$apCount = Db::name('supplier_goods_appraises')->where(['orderGoodsId'=>$goods['id'],'dataFlag'=>1])->count();
		                if($apCount>0)continue;
		    	    	//增加订单评价
						$data = [];
						$data['userId'] = $order->userId;
						$data['goodsSpecId'] = (int)$goods['goodsSpecId'];
						$data['orderGoodsId'] = $goods['id'];
						$data['goodsId'] = $goods['goodsId'];
						$data['supplierId'] = $order->supplierId;
						$data['orderId'] = $goods['orderId'];
						$data['goodsScore'] = 5;
						$data['serviceScore'] = 5;
						$data['timeScore']= 5;
						$data['content'] = '自动好评';
						$data['createTime'] = date('Y-m-d H:i:s');
						Db::name('supplier_goods_appraises')->insert($data);
		    	    }
					//增加商品评分
					$updateSql = "update ".$prefix."supplier_goods_scores set 
						             totalScore=totalScore+15,
					             goodsScore=goodsScore+5,
					             serviceScore=serviceScore+5,
					             timeScore=timeScore+5,
					             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
					             where goodsId=".$goods['goodsId'];
					Db::execute($updateSql);
					//增加商品评价数
					Db::name('supplier_goods')->where('goodsId',$goods['goodsId'])->setInc('appraiseNum');
					//增加店铺评分
					$updateSql = "update ".$prefix."supplier_scores set 
					             totalScore=totalScore+15,
					             goodsScore=goodsScore+5,
					             serviceScore=serviceScore+5,
					             timeScore=timeScore+5,
					             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
					             where supplierId=".$order->supplierId;
					Db::execute($updateSql);
					// 查询该订单是否已经完成评价,修改orders表中的isAppraise
					$ogRs = Db::name('supplier_order_goods')->alias('og')
					   			  ->join('supplier_goods_appraises ga','og.orderId=ga.orderId and og.goodsId=ga.goodsId and og.goodsSpecId=ga.goodsSpecId','left')
					              ->where('og.orderId',$order->orderId)->field('og.id,ga.id gid')->select();
					$isFinish = true;
					foreach ($ogRs as $vkey => $v){
						if($v['id']>0 && $v['gid']==''){
								$isFinish = false;
								break;
						}
					}
					//订单商品全部评价完则修改订单状态
					if($isFinish){
						if(WSTConf("CONF.isAppraisesScore")==1){
							$appraisesScore = (int)WSTConf('CONF.appraisesScore');
							if($appraisesScore>0){
								//给用户增加积分
								$score = [];
								$score['userId'] = $order->userId;
								$score['score'] = $appraisesScore;
								$score['dataSrc'] = 1;
								$score['dataId'] = $order->orderId;
								$score['dataRemarks'] = "评价订单【".$order->orderNo."】获得积分".$appraisesScore."个";
								$score['scoreType'] = 1;
								$score['createTime'] = date('Y-m-d H:i:s');
								Db::name('user_scores')->insert($score);
								// 增加用户积分
							    model('Users')->where("userId=".$order->userId)->update([
							    	'userScore'=>Db::Raw('userScore+'.$appraisesScore),
							    	'userTotalScore'=>Db::Raw('userTotalScore+'.$appraisesScore)
							    ]);
							}
						}
					}
				}
		        Db::commit();
				return WSTReturn('操作成功',1);
	 		}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
	 	}
	 	return WSTReturn('操作成功',1);
	}
	/**
	 * 自动确认收货
	 */
	public function autoReceive(){
	 	$autoReceiveDays = (int)WSTConf('CONF.autoReceiveDays');
	 	$autoReceiveDays = ($autoReceiveDays>0)?$autoReceiveDays:10;//避免有些客户没有设置值
	 	$lastDay = date("Y-m-d 00:00:00",strtotime("-".$autoReceiveDays." days"));
	 	$rs = model('supplier/SupplierOrders')->where([['deliveryTime','<',$lastDay],['orderStatus','=',1],['dataFlag','=',1]])->field("orderId,orderNo,supplierId,userId,commissionFee")->select();
	 	if(!empty($rs)){
	 		$prefix = config('database.prefix');
	 		Db::startTrans();
		    try{
		    	//结束订单状态
	 			$limitDay = (int)WSTConf('CONF.afterSaleServiceDays');
				// 售后结束时间
				$afterSaleEndTime = date('Y-m-d H:i:s', strtotime("+{$limitDay} day"));
		 		foreach ($rs as $key => $order){
		 			$order->afterSaleEndTime = $afterSaleEndTime;
		 			$order->receiveTime = date('Y-m-d 00:00:00');
		 			$order->orderStatus = 2;
		 			$rsStatus = $order->save();
		 			if(false !== $rsStatus){

		 				//修改商品成交量
						$goodss = Db::name('supplier_order_goods')->where('orderId',$order['orderId'])->field('goodsId,goodsNum,goodsSpecId')->select();
						foreach($goodss as $key =>$v){
							Db::name('supplier_goods')->where('goodsId',$v['goodsId'])->update([
	                            'saleNum'=>Db::raw('saleNum+'.$v['goodsNum'])
	                        ]);
							if($v['goodsSpecId']>0){
								Db::name('supplier_goods_specs')->where('id',$v['goodsSpecId'])->update([
		                            'saleNum'=>Db::raw('saleNum+'.$v['goodsNum'])
		                        ]);
							}
						}
					
				    	//修改商家未计算订单数
						$torder = Db::name('supplier_orders')->where("orderId",$order->orderId)->field("orderId,commissionFee")->find();
						Db::name('suppliers')->where('supplierId',$order->supplierId)->update([
							'noSettledOrderNum'=>Db::raw('noSettledOrderNum+1'),
							'noSettledOrderFee'=>Db::raw('noSettledOrderFee-'.$torder['commissionFee'])
						]);
					    
		 				
	                    //新增订单日志
						$logOrder = [];
						$logOrder['orderId'] = $order->orderId;
						$logOrder['orderStatus'] = 2;
						$logOrder['logContent'] = "系统自动确认收货";
						$logOrder['logUserId'] = $order->userId;
						$logOrder['logType'] = 0;
						$logOrder['logTime'] = date('Y-m-d H:i:s');
						Db::name('supplier_log_orders')->insert($logOrder);

						//发送一条商家信息
						$tpl = WSTMsgTemplates('ORDER_ATUO_RECEIVE');
		                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
		                    $find = ['${ORDER_NO}'];
		                    $replace = [$order['orderNo']];
		                	$msg = array();
				            $msg["supplierId"] = $order['supplierId'];
				            $msg["tplCode"] = $tpl["tplCode"];
				            $msg["msgType"] = 1;
				            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']) ;
				            $msg["msgJson"] = ['from'=>1,'dataId'=>$order->orderId];
				            model("common/SupplierMessageQueues")->add($msg);
		                }
		 			}
	 			}
	 			Db::commit();
				return WSTReturn('操作成功',1);
	 		}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
	 	}
	 	return WSTReturn('操作成功',1);
	}

	/**
	 * 商家订单自动结算
	 * @return [type] [description]
	 */
	public function autoSupplierSettlement(){
		$now = date("Y-m-d H:i:s");
    	$where = [];
    	$where[] = ["settlementId","=",0];
    	$where[] = ["afterSaleEndTime","<",$now];
    	$where[] = ["orderStatus","=",2];
    	$olist = Db::name("supplier_orders")
		    	->where($where)
		    	->field("orderId,orderNo")
		    	->select();
		if(count($olist)>0){
			$orderIds = [];
			foreach ($olist as $key => $vo) {
				$orderIds[] = $vo["orderId"];
			}
			$where = [];
			$where[] = ["isClose","=",0];
			$where[] = ["serviceStatus","not in",[5,6]];
			$where[] = ["orderId","in",$orderIds];
			$list = Db::name('supplier_order_services')
					->where($where)
					->field("orderId,count(orderId) cnt")
					->group("orderId")
					->select();
			$omaps = [];
			foreach ($list as $key => $vo) {
				$omaps[$vo["orderId"]] = $vo["cnt"];
			}
			
			foreach ($olist as $key => $vo) {
				//已过售后期并且没有未处理完的售后单，可进行结算
				if(!isset($omaps[$vo["orderId"]])){
					Db::startTrans();
				    try{
						model('common/SupplierSettlements')->speedySettlement($vo["orderId"]);
						Db::commit();
						return WSTReturn('结算成功',1);
			 		}catch (\Exception $e) {
			            Db::rollback();
			            return WSTReturn('结算失败',-1);
			        }
				}
			}
		}
		return WSTReturn('结算成功',1);
	}
	
}