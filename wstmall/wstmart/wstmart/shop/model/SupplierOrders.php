<?php
namespace wstmart\shop\model;
use think\Db;
use Env;
use think\Loader;
use wstmart\common\model\LogSms;
use wstmart\common\model\SupplierOrderRefunds as M;
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
 * 订单业务处理类
 */
class SupplierOrders extends Base{
	protected $pk = 'orderId';
	
	/**
	 * 正常订单
	 */
	public function submit($orderSrc = 0){
		$addressId = (int)input('post.s_addressId');
		$deliverType = ((int)input('post.deliverType')!=0)?1:0;
		
		$payType = ((int)input('post.payType')!=0)?1:0;
		$userId = (int)session('WST_USER.userId');
		if($userId==0)return WSTReturn('下单失败，请先登录');
		$shopId = (int)session('WST_USER.shopId');
		//检测购物车
		$carts = model('supplierCarts')->getCarts(true, $userId);
		if(empty($carts['carts']))return WSTReturn("下单失败，请选择有效的库存商品");
		
		//检测地址是否有效
		$address = Db::name('user_address')->where(['userId'=>$userId,'addressId'=>$addressId,'dataFlag'=>1])->find();
		if(empty($address)){
			return WSTReturn("无效的用户地址");
		}
	    $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$address['areaIdPath']);
        $address['areaId2'] = $tmp[1];//记录配送城市
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::name('areas')->where([['areaId','in',$areaIds],['dataFlag','=',1]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$address['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $address['areaName'] = implode('',$areaNames);
	         }
        }
		$address['userAddress'] = $address['areaName'].$address['userAddress'];
		WSTUnset($address, 'isDefault,dataFlag,createTime,userId');
		
		//生成订单
		Db::startTrans();
		try{
			$orderunique = WSTOrderQnique();
			foreach ($carts['carts'] as $ckey =>$supplierOrder){
				$orderNo = WSTOrderNo(); 
				$supplierId = $supplierOrder['supplierId'];
				$deliverType = ((int)input('post.deliverType_'.$supplierId)!=0)?1:0;

				//创建订单
				$order = [];
				$order = array_merge($order,$address);
				$order['orderNo'] = $orderNo;
				$order['userId'] = $userId;
				$order['shopId'] = $shopId;
				$order['supplierId'] = $supplierId;
				$order['payType'] = $payType;
				$order['goodsMoney'] = $supplierOrder['goodsMoney'];
				//计算运费和总金额
				$order['deliverType'] = $deliverType;
				if($supplierOrder['isFreeShipping']){
                    $order['deliverMoney'] = 0;
				}else{
					$order['deliverMoney'] = ($deliverType==1)?0:$this->getOrderFreight($supplierId,$order['areaId2'],$supplierOrder);				
				}
				
				$order['totalMoney'] = $order['goodsMoney']+$order['deliverMoney'];
                //积分支付-计算分配积分和金额
                
				//实付金额要减去积分兑换的金额和店铺总优惠
				$order['realTotalMoney'] = WSTPositiveNum($order['totalMoney'] - $supplierOrder['promotionMoney']);
				$order['needPay'] = $order['realTotalMoney'];
                if($payType==1){
                	if($order['needPay']>0){
                        $order['orderStatus'] = -2;//待付款
				        $order['isPay'] = 0; 
                	}else{
                        $order['orderStatus'] = 0;//待发货
				        $order['isPay'] = 1;
				        $order['payTime'] = date('Y-m-d H:i:s');
						$order['payFrom'] = 'others'; 
						if($deliverType==1)$order['verificationCode'] = WSTOrderVerificationCode($supplierId,1);
                	}
				}else{
					$order['orderStatus'] = 0;//待发货
					if($order['needPay']==0){
						$order['isPay'] = 1; 
						$order['payFrom'] = 'others';
						$order['payTime'] = date('Y-m-d H:i:s');
					}
					if($deliverType==1)$order['verificationCode'] = WSTOrderVerificationCode($supplierId,1);
				}
				

				if($supplierOrder['isInvoice']==1){
					$isInvoice = ((int)input('post.isInvoice_'.$supplierId)!=0)?1:0;
					$invoiceClient = ($isInvoice==1)?input('post.invoiceClient_'.$supplierId):'';
					$order['isInvoice'] = $isInvoice;
					if($isInvoice==1){
						$order['invoiceJson'] = model('invoices')->getInviceInfo((int)input('param.invoiceId_'.$supplierId),$userId);// 发票信息
						$order['invoiceClient'] = $invoiceClient;
					}else{
						$order['invoiceJson'] = '';// 发票信息
						$order['invoiceClient'] = '';
					}
				}else{
					$order['isInvoice'] = 0;
					$order['invoiceJson'] = '';// 发票信息
					$order['invoiceClient'] = '';
				}
				
				$order['orderRemarks'] = input('post.remark_'.$supplierId);
				$order['orderunique'] = $orderunique;
				$order['orderSrc'] = $orderSrc;
				$order['dataFlag'] = 1;
				$order['payRand'] = 1;
				$order['createTime'] = date('Y-m-d H:i:s');
				$result = $this->data($order,true)->isUpdate(false)->allowField(true)->save($order);
				if(false !== $result){
					$orderId = $this->orderId;
					$orderTotalGoods = [];
					$commissionFee = 0;

					/**
					 * 计算订单中的商品可优惠多少金额
					 */
					// 1.先计算出商品总价  $order['goodsMoney']为订单下的商品总价

					
					// 订单下最后一件商品索引
					$lastOgIndex = count($supplierOrder['list'])-1;
					foreach ($supplierOrder['list'] as $gkey =>$goods){
						//创建订单商品记录
						$orderGgoods = [];
						$orderGoods['orderId'] = $orderId;
						$orderGoods['goodsId'] = $goods['goodsId'];
						$orderGoods['goodsNum'] = $goods['cartNum'];
						$orderGoods['goodsPrice'] = $goods['supplierPrice'];
						$orderGoods['goodsSpecId'] = $goods['goodsSpecId'];
						if(!empty($goods['specNames'])){
							$specNams = [];
							foreach ($goods['specNames'] as $pkey =>$spec){
								$specNams[] = $spec['catName'].'：'.$spec['itemName'];
							}
							$orderGoods['goodsSpecNames'] = implode('@@_@@',$specNams);
						}else{
							$orderGoods['goodsSpecNames'] = '';
						}
						$orderGoods['goodsName'] = $goods['goodsName'];
						$orderGoods['goodsImg'] = $goods['goodsImg'];
						$orderGoods['commissionRate'] = WSTGoodsCommissionRate($goods['goodsCatId']);
						$orderGoods['goodsCode'] = '';
						$orderGoods['goodsType'] = 0;
						$orderGoods['extraJson'] = '';
						$orderGoods['promotionJson'] = '';

						$orderGoods["orderGoodscommission"] = 0;
						//计算订单总佣金
                        if((float)$orderGoods['commissionRate']>0){
                        	$orderGoodscommission = round($orderGoods['goodsPrice']*$orderGoods['goodsNum']*$orderGoods['commissionRate']/100,2);
                        	$orderGoods["orderGoodscommission"] = $orderGoodscommission;
                        	$commissionFee += $orderGoodscommission;
                        }

                        $orderTotalGoods[] = $orderGoods;

						//修改库存
						if($goods['goodsSpecId']>0){
					        Db::name('supplier_goods_specs')->where('id',$goods['goodsSpecId'])->update([
                                'specStock'=>Db::raw('specStock-'.$goods['cartNum'])
					        ]);
						}
                        Db::name('supplier_goods')->where('goodsId',$goods['goodsId'])->update([
                            'goodsStock'=>Db::raw('goodsStock-'.$goods['cartNum'])
                        ]);
					}

					Db::name('supplier_order_goods')->insertAll($orderTotalGoods);
					//更新订单佣金
					$this->where('orderId',$orderId)->update(['commissionFee'=>$commissionFee]);

					//建立订单记录
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = ($payType==1 && $order['needPay']==0)?-2:$order['orderStatus'];
					$logOrder['logContent'] = ($payType==1)?"下单成功，等待用户支付":"下单成功";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('supplier_log_orders')->insert($logOrder);
					if($payType==1 && $order['needPay']==0){
						$logOrder = [];
						$logOrder['orderId'] = $orderId;
						$logOrder['orderStatus'] = 0;
						$logOrder['logContent'] = "订单已支付，下单成功";
						$logOrder['logUserId'] = $userId;
						$logOrder['logType'] = 0;
						$logOrder['logTime'] = date('Y-m-d H:i:s');
						Db::name('supplier_log_orders')->insert($logOrder);
					}

					if($deliverType==1){//自提
						//自提订单（已支付）发送核验码
						if(($payType==1 && $order['needPay']==0) || $payType==0){
							$tpl = WSTMsgTemplates('PHONE_USER_ORDER_VERIFICATCODE');
					        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
					        	$userPhone = $order['userPhone'];
					        	$supplier = Db::name("suppliers")->where(["supplierId"=>$supplierOrder['supplierId']])->field("supplierName,supplierAddress")->find();
					        	$supplierName = $supplier["supplierName"];
					        	$supplierAddress = $supplier["supplierAddress"];
					        	$splieVerificationCode = join(" ",str_split($order['verificationCode'],4));
					            $params = ['tpl'=>$tpl,'params'=>['MALL_NAME'=>WSTConf('CONF.mallName'),'ORDER_CODE'=>$splieVerificationCode,'SHOP_NAME'=>$supplierName,'SHOP_ADDRESS'=>$supplierAddress]];
					            model("common/LogSms")->sendSMS(0,$userPhone,$params,'submit','',$userId,0);
					        }
						}
				    }

					//给店铺增加提示消息
					$tpl = WSTMsgTemplates('ORDER_SUBMIT');
	                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                    $find = ['${ORDER_NO}'];
	                    $replace = [$orderNo];
	                    
	                	$msg = array();
			            $msg["supplierId"] = $supplierOrder['supplierId'];
			            $msg["tplCode"] = $tpl["tplCode"];
			            $msg["msgType"] = 1;
			            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
			            $msg["msgJson"] = ['from'=>1,'dataId'=>$orderId];
			            model("common/SupplierMessageQueues")->add($msg);
	                }
				}
			}
			//删除已选的购物车商品
			Db::name('supplier_carts')->where(['userId'=>$userId,'isCheck'=>1])->delete();
			Db::commit();
			return WSTReturn("提交订单成功", 1,$orderunique);
		}catch (\Exception $e) {
			//print_r($e);
            Db::rollback();
            return WSTReturn('提交订单失败',-1);
        }
	}
	
	/**
	 * 分配金额和积分
	 */
	public function allocOrderMoney($useMoney,$totalOrderMoney,$orderMoney){
		 if($useMoney>$totalOrderMoney)$useMoney = $totalOrderMoney;
         return round(($useMoney*$orderMoney)/$totalOrderMoney,2);
	}

	
	
	/**
	 * 根据订单唯一流水获取订单信息
	 */
	public function getByUnique($shopId=0,$orderNo=0,$isBatch=0){
		
		$shopId = (int)session('WST_USER.shopId');
		if($isBatch==1){
			$rs = $this->where(['shopId'=>$shopId,'orderunique'=>$orderNo])->field('orderId,orderNo,payType,needPay,orderunique,deliverMoney,userName,userPhone,userAddress')->select();
		}else{
			$rs = $this->where(['shopId'=>$shopId,'orderNo'=>$orderNo])->field('orderId,orderNo,payType,needPay,orderunique,deliverMoney,userName,userPhone,userAddress')->select();
		}
		
		$data = [];
		$data['orderunique'] = $orderNo;
		$data['list'] = [];
		$payType = 0;
		$totalMoney = 0;
		$orderIds = [0];
		foreach ($rs as $key =>$v){
			if($v['payType']==1)$payType = 1;
			$totalMoney = $totalMoney + $v['needPay'];
			$orderIds[] = $v['orderId'];
			$data['list'][] = $v;
		}
		$data['totalMoney'] = $totalMoney;
		$data['payType'] = $payType;
		//获取商品信息
		$goods = Db::name('supplier_order_goods')->where([['orderId','in',$orderIds]])->select();
		foreach ($goods as $key =>$v){
			
			$shotGoodsSpecNames = [];
    	 	if($v['goodsSpecNames']!=""){
    	 		$v['goodsSpecNames'] = str_replace('：',':',$v['goodsSpecNames']);
    	 		$goodsSpecNames = explode('@@_@@',$v['goodsSpecNames']);
    	 		
	    	 	foreach ($goodsSpecNames as $key => $spec) {
	    	 	 	$obj = explode(":",$spec);
	    	 	 	$shotGoodsSpecNames[] = $obj[1];
	    	 	}
    	 	}
    	 	$v['shotGoodsSpecNames'] = implode('，',$shotGoodsSpecNames);

			if($v['goodsSpecNames']!=''){
				$v['goodsSpecNames'] = explode('@@_@@',$v['goodsSpecNames']);
			}else{
				$v['goodsSpecNames'] = [];
			}
			$data['goods'][$v['orderId']][] = $v;
			
		}
		//如果是在线支付的话就要加载支付信息
		if($data['payType']==1){
			//获取支付信息
			$payments = model('payments')->where(['isOnline'=>1,'enabled'=>1])->order('payOrder asc')->select();
			$data['payments'] = $payments;
		}
		return $data;
	}
	
	/**
	 * 获取用户订单列表
	 */
	public function userOrdersByPage($orderStatus, $isAppraise = -1){
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$orderNo = input('post.orderNo');
		$supplierName = input('post.supplierName');
		$isRefund = (int)input('post.isRefund',-1);
		$where = ['o.shopId'=>$shopId,'o.dataFlag'=>1];
        $condition = [];
		if(is_array($orderStatus)){
			$condition[] = ['orderStatus','in',$orderStatus];
		}else{
			$where['orderStatus'] = $orderStatus;
		}
		if($isAppraise!=-1)$where['isAppraise'] = $isAppraise;
		if($orderNo!=''){
			$condition[] = ['o.orderNo','like',"%$orderNo%"];
		}
		if($supplierName != ''){
			$condition[] = ['s.supplierName','like',"%$supplierName%"];
		}
		if(in_array($isRefund,[0,1])){
			$where['isRefund'] = $isRefund;
		}
		$page = $this->alias('o')->join('suppliers s','o.supplierId=s.supplierId','left')
		             ->join('supplier_order_complains oc','oc.orderId=o.orderId','left')
		             ->join('supplier_order_refunds orf','orf.orderId=o.orderId and orf.refundStatus!=-1','left')
		             ->where($where)->where($condition)
		             ->field('o.afterSaleEndTime,o.receiveTime,o.orderRemarks,o.noticeDeliver,o.orderId,o.orderNo,s.supplierName,s.supplierId,s.supplierQQ,s.supplierWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.deliverType,deliverMoney,o.isPay,payType,payFrom,o.orderStatus,needPay,isAppraise,o.isRefund,orderSrc,o.createTime,oc.complainId,orf.id refundId,o.orderCode')
			         ->order('o.createTime', 'desc')
			         ->group('o.orderId')
			         ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['data'])>0){

	    	 $orderIds = [];
	    	 foreach ($page['data'] as $v){
	    	 	 $orderIds[] = $v['orderId'];
	    	 }
	    	 $goods = Db::name('supplier_order_goods')->where([['orderId','in',$orderIds]])->select();
	    	 $goodsMap = [];
	    	 foreach ($goods as $v){
	    	 	$v['goodsName'] = WSTStripTags($v['goodsName']);
	    	 	$shotGoodsSpecNames = [];
	    	 	if($v['goodsSpecNames']!=""){
	    	 		$v['goodsSpecNames'] = str_replace('：',':',$v['goodsSpecNames']);
	    	 		$goodsSpecNames = explode('@@_@@',$v['goodsSpecNames']);
	    	 		
		    	 	foreach ($goodsSpecNames as $key => $spec) {
                        if(strpos($spec, ':') !== FALSE) {
                            $obj = explode(":",$spec);
                            $shotGoodsSpecNames[] = $obj[1];
                        }
		    	 	}
	    	 	}
	    	 	$v['shotGoodsSpecNames'] = implode('，',$shotGoodsSpecNames);
	    	 	$v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
	    	 	$goodsMap[$v['orderId']][] = $v;
	    	 }
             // 查询一个订单下是否有物流包裹
	    	 foreach ($page['data'] as $key => $v){
                 $orderExpress = Db::name('supplier_order_express')->field('expressId,expressNo')->where([['orderId','=',$v['orderId']],['isExpress','=',1]])->count();
                 $page['data'][$key]['hasExpress'] = ($orderExpress>0)?true:false;
	    	 	 $page['data'][$key]['allowRefund'] = 0;
	    	 	 //只要是已支付的，并且没有退款的，都可以申请退款操作
	    	 	 if($v['payType']==1 && $v['isRefund']==0 && $v['refundId']=='' && ($v['isPay'] ==1)){
                      $page['data'][$key]['allowRefund'] = 1;
	    	 	 }
	    	 	 //货到付款中使用了积分支付的也可以申请退款
	    	 	 if($v['payType']==0 && $v['refundId']=='' && $v['isRefund']==0){
                      $page['data'][$key]['allowRefund'] = 1;
	    	 	 }
	    	 	 $page['data'][$key]['list'] = $goodsMap[$v['orderId']];
	    	 	 $page['data'][$key]['isComplain'] = 1;
	    	 	 if(($v['complainId']=='') && ($v['payType']==0 || ($v['payType']==1 && $v['orderStatus']!=-2))){
	    	 	 	$page['data'][$key]['isComplain'] = '';
	    	 	 }
	    	 	 $page['data'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
	    	 	 $page['data'][$key]['deliverTypeName'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['data'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    		 $page['data'][$key]['orderCodeTitle'] = WSTOrderModule($v['orderCode']);
	    	 	 if($v["orderStatus"]==-2){
					$page['data'][$key]['pkey'] = WSTBase64urlEncode($v["orderNo"]."@0");
				}
				// 是否可申请售后
				$page['data'][$key]['canAfterSale'] = false;
				// 订单已确认收货
				if($v['payType']==1 && $v['orderStatus']==2){
					// 判断是否已超过售后服务有效期
					// 如果 当前时间>(确认收货时间+售后服务期限) 表示无法继续申请售后
					$now = time();
					// 售后结束日期
					$endTime = strtotime($v['afterSaleEndTime']);
					$_rs = ($now<=$endTime);
					$page['data'][$key]['canAfterSale'] = $_rs;
					if($_rs){
						// 判断订单是否还能继续申请售后 【订单商品总数-售后单商品总数>0】
						$ogNum = Db::name('supplier_order_goods')
								 ->where(['orderId'=>$v['orderId']])
								 ->value('sum(goodsNum) ogNum');
						$osNum = Db::name('supplier_order_services')->alias('os')
															 ->join('supplier_orders o','o.orderId=os.orderId','inner')
															 ->join('supplier_service_goods sg','sg.serviceId=os.id')
															 ->where(['o.orderId'=>$v['orderId'],'os.isClose'=>0])
															 ->value('sum(sg.goodsNum) osNum');
						$page['data'][$key]['canAfterSale'] = ($ogNum>$osNum);
					}
				}
	    	 }
	    }
	    return $page;
	}
	
	/**
	 * 用户收货[同时给外部虚拟商品收货调用]
	 * 注意：修改该函数的逻辑，要留意一同修改管理员修改订单的函数逻辑[admin/model/orders->receiveOrder]
	 */
	public function receive($orderId = 0,$userId = 0){
		
        $orderId = (int)input('post.id');
	    $shopId = (int)session('WST_USER.shopId');
	    $userId = (int)session('WST_USER.userId');
		
		$order = $this->alias('o')->join('suppliers s','o.supplierId=s.supplierId','left')
		              ->where(['o.shopId'=>$shopId,'o.orderId'=>$orderId,'o.orderStatus'=>1])
		              ->field('o.orderId,o.orderNo,o.payType,s.userId,s.supplierId,o.realTotalMoney,commissionFee')->find();
		if(!empty($order)){
			Db::startTrans();
		    try{
				$limitDay = (int)WSTConf('CONF.afterSaleServiceDays');
				// 售后结束时间
				$afterSaleEndTime = date('Y-m-d H:i:s', strtotime("+{$limitDay} day"));
				$data = ['orderStatus'=>2,'receiveTime'=>date('Y-m-d H:i:s'),'afterSaleEndTime'=>$afterSaleEndTime];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
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
					$torder = Db::name('supplier_orders')->where("orderId",$orderId)->field("orderId,commissionFee")->find();
					Db::name('suppliers')->where('supplierId',$order['supplierId'])->update([
						'noSettledOrderNum'=>Db::raw('noSettledOrderNum+1'),
						'noSettledOrderFee'=>Db::raw('noSettledOrderFee-'.$torder['commissionFee'])
					]);
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 2;
					$logOrder['logContent'] = "用户已收货";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('supplier_log_orders')->insert($logOrder);
					
					Db::commit();
					return WSTReturn('操作成功',1);
				}
		    }catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败'.$e->getMessage(),-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户取消订单
	 * 注意：修改该函数的逻辑，要留意一同修改管理员修改订单的函数逻辑[admin/model/orders->cancelOrder]
	 */
	public function cancel(){
		$orderId = (int)input('post.id');
		$reason = (int)input('post.reason');
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$order = $this->alias('o')->join('suppliers s','o.supplierId=s.supplierId','left')
		              ->where([['o.orderStatus','in',[-2,0]],['o.shopId','=',$shopId],['o.orderId','=',$orderId]])
		              ->field('o.orderId,o.orderNo,s.userId,s.supplierId,o.orderCode,o.isPay,o.orderType,o.payType,o.orderStatus,o.realTotalMoney')->find();
		$reasonData = WSTDatas('ORDER_CANCEL',$reason);
		if(empty($reasonData))return WSTReturn("无效的取消原因");

		if(!empty($order)){
			Db::startTrans();
		    try{
                $data = ['orderStatus'=>-1,'cancelReason'=>$reason];
				//把实付金额设置为0
				if($order['payType']==0 || ($order['payType']==1 && $order['isPay']==0)){
					$data['realTotalMoney'] = 0;
					$order['realTotalMoney'] = 0;
					$data['isClosed'] = 1;
				}
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
                    //正常订单商品库存处理
                    $goods = Db::name('supplier_order_goods')->alias('og')->join('supplier_goods g','og.goodsId=g.goodsId','inner')
						           ->where('orderId',$orderId)->field('og.*,g.isSpec')->select();
                    //返还商品库存
					foreach ($goods as $key => $v){
						
						if($order['orderCode']=='order'){
							//修改库存
							if($v['isSpec']>0){
						        Db::name('supplier_goods_specs')->where('id',$v['goodsSpecId'])->setInc('specStock',$v['goodsNum']);
							}
							Db::name('supplier_goods')->where('goodsId',$v['goodsId'])->setInc('goodsStock',$v['goodsNum']);
						}
						
                    }
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = -1;
					$logOrder['logContent'] = "用户取消订单，取消原因：".$reasonData['dataName'];
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('supplier_log_orders')->insert($logOrder);
					//发送一条商家信息
					$tpl = WSTMsgTemplates('ORDER_CANCEL');
	                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                	
	                    $find = ['${ORDER_NO}','${REASON}'];
	                    $replace = [$order['orderNo'],$reasonData['dataName']];
	                   
	                	$msg = array();
			            $msg["supplierId"] = $order["supplierId"];
			            $msg["tplCode"] = $tpl["tplCode"];
			            $msg["msgType"] = 1;
			            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
			            $msg["msgJson"] = ['from'=>1,'dataId'=>$orderId];
			            model("common/SupplierMessageQueues")->add($msg);
	                }
					
                    //取消订单自动申请退款
                    if($order['isPay']==1){
	                    $m = new M();
	                    $m->autoApplyRefund($orderId,$reasonData['dataName'],$order['realTotalMoney'],$order['orderNo']);
                    }
					Db::commit();
					return WSTReturn('订单取消成功',1);
				}
			}catch (\Exception $e) {
		        Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户拒收订单
	 */
	public function reject(){
		$orderId = (int)input('post.id');
		$reason = (int)input('post.reason');
		$content = input('post.content');
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$order = $this->alias('o')->join('suppliers s','o.supplierId=s.supplierId','left')
		              ->where(['o.shopId'=>$shopId,'o.orderId'=>$orderId,'o.orderStatus'=>1])
		              ->field('o.orderId,o.orderNo,o.supplierId,s.userId,payType,o.userAddress,o.userName,o.realTotalMoney')->find();
		$reasonData = WSTDatas('ORDER_REJECT',$reason);
		if(empty($reasonData))return WSTReturn("无效的拒收原因");
		if($reason==10000 && $content=='')return WSTReturn("请输入拒收原因");
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>-3,'rejectReason'=>$reason];
				if($reason==10000)$data['rejectOtherReason'] = $content;
				//如果是货到付款拒收的话，把实付金额设置为0
				if($order['payType']==0)$data['realTotalMoney'] = 0;
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = -3;
					$logOrder['logContent'] = "用户拒收订单，拒收原因：".$reasonData['dataName'].(($reason==10000)?"-".$content:"");
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('supplier_log_orders')->insert($logOrder);
					//发送一条商家信息
					$tpl = WSTMsgTemplates('ORDER_REJECT');
	                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                    $find = ['${ORDER_NO}','${REASON}'];
	                    $replace = [$order['orderNo'],$reasonData['dataName'].(($reason==10000)?"-".$content:"")];
	                   
	                	$msg = array();
			            $msg["supplierId"] = $order['supplierId'];
			            $msg["tplCode"] = $tpl["tplCode"];
			            $msg["msgType"] = 1;
			            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
			            $msg["msgJson"] = ['from'=>1,'dataId'=>$orderId];
			            model("common/SupplierMessageQueues")->add($msg);
	                }
					

					Db::commit();
					return WSTReturn('操作成功',1);
				}
			}catch (\Exception $e) {
		        Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 获取订单价格
	 */
	public function getMoneyByOrder($orderId = 0){
		$orderId = ($orderId>0)?$orderId:(int)input('post.id');
		return $this->where('orderId',$orderId)->field('orderId,orderNo,goodsMoney,deliverMoney,totalMoney,realTotalMoney')->find();
	}
	

	
	/**
	 * 获取订单详情
	 */
	public function getByView($orderId, $uId=0){
		$shopId = (int)session('WST_USER.shopId');
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$orders = Db::name('supplier_orders')->alias('o')
		               ->join('suppliers s','o.supplierId=s.supplierId','left')
		               ->join('supplier_order_complains oc','oc.orderId=o.orderId','left')
		               ->join('supplier_order_refunds orf ','o.orderId=orf.orderId','left')
		               ->where('o.dataFlag=1 and o.orderId='.$orderId.' and ( o.shopId='.$shopId.')')
		               ->field('o.*,s.areaId supplierAreaId,s.supplierAddress,s.supplierTel,s.supplierName,s.supplierQQ,s.supplierWangWang,orf.id refundId,orf.refundRemark,orf.refundStatus,orf.refundTime,orf.backMoney,orf.backMoney,oc.complainId')->find();
		if(empty($orders))return WSTReturn("无效的订单信息");
		// 获取店铺地址
		$orders['supplierAddr'] = model('common/areas')->getParentNames($orders['supplierAreaId']);
		$orders['supplierAddress'] = implode('',$orders['supplierAddr']).$orders['supplierAddress'];
		unset($orders['supplierAddr']);
		//下单用户
		$orderUser = Db::name("users")->where(["userId"=>$orders['userId']])->field("userId,userName,loginName")->find();
		$orders['orderUser'] = $orderUser;
		//获取订单信息
		$log = Db::name('supplier_log_orders')->where('orderId',$orderId)->order('logId asc')->select();
		$orders['log'] = [];
		$logFilter = [];
		foreach ($log as $key => $v) {
			if(in_array($orders['orderStatus'],[-2,0,1,2]) && in_array($v['orderStatus'],$logFilter))continue;
			$orders['log'][] = $v; 
			$logFilter[] = $v['orderStatus'];
		}
		//获取订单商品
		$orders['goods'] = Db::name('supplier_order_goods')->alias('og')->join('supplier_goods g','g.goodsId=og.goodsId','left')->where('orderId',$orderId)->field('og.*,g.goodsSn')->order('id asc')->select();
		foreach ($orders['goods'] as $key => $v) {
		 	$orders['goods'][$key]['goodsName'] = WSTStripTags($v['goodsName']);
			//如果是虚拟商品
			if($orders['orderType']==1){
				$orders['goods'][$key]['extraJson'] = json_decode($v['extraJson'],true);
			}
			$shotGoodsSpecNames = [];
		 	if($v['goodsSpecNames']!=""){
		 		$v['goodsSpecNames'] = str_replace('：',':',$v['goodsSpecNames']);
		 		$goodsSpecNames = explode('@@_@@',$v['goodsSpecNames']);
		 		
	    	 	foreach ($goodsSpecNames as $key2 => $spec) {
	    	 	 	$obj = explode(":",$spec);
	    	 	 	$shotGoodsSpecNames[] = $obj[1];
	    	 	}
		 	}
		 	$orders['goods'][$key]['shotGoodsSpecNames'] = implode('，',$shotGoodsSpecNames);
		}
		
        // 发货时间与快递单号
        $orderExpressNos = Db::name('supplier_order_express')->where([['orderId','=',$orderId],['isExpress','=',1]])->column("expressNo");
        if($orderExpressNos){
            // 多张快递单号用逗号拼接，并过滤掉没有单号的
            $orders["expressNo"] = implode(",",array_filter($orderExpressNos));
        }else{
            $orders["expressNo"] = '';
        }
        //格式化发票信息
		if($orders['isInvoice']==1){
			$orders['invoice'] = json_decode($orders['invoiceJson'],true);
		}
		$orders['isComplain'] = 1;
		if(($orders['complainId']=='') && ($orders['payType']==0 || ($orders['payType']==1 && $orders['orderStatus']!=-2))){
			$orders['isComplain'] = '';
		}
		

		$orders['allowRefund'] = 0;
	 	//只要是已支付的，并且没有退款的，都可以申请退款操作
	 	if($orders['payType']==1 && $orders['isRefund']==0 && $orders['refundId']=='' && ($orders['isPay'] ==1)){
              $orders['allowRefund'] = 1;
	 	}
	 	//货到付款中使用了积分支付的也可以申请退款
	 	if($orders['payType']==0 && $orders['refundId']=='' && $orders['isRefund']==0){
              $orders['allowRefund'] = 1;
	 	}
		// 是否可申请售后
		$orders['canAfterSale'] = false;
		// 订单已确认收货
		if($orders['payType']==1 && $orders['orderStatus']==2){
			// 判断是否已超过售后服务有效期
			// 如果 当前时间>(确认收货时间+售后服务期限) 表示无法继续申请售后
			$now = time();
			// 售后结束日期
			$endTime = strtotime($orders['afterSaleEndTime']);
			$_rs = ($now<=$endTime);
			$orders['canAfterSale'] = $_rs;
			if($_rs){
				// 判断订单是否还能继续申请售后 【订单商品总数-售后单商品总数>0】
				$ogNum = Db::name('supplier_order_goods')
						 ->where(['orderId'=>$orderId])
						 ->value('sum(goodsNum) ogNum');
				$osNum = Db::name('supplier_order_services')->alias('os')
													 ->join('orders o','o.orderId=os.orderId','inner')
													 ->join('service_goods sg','sg.serviceId=os.id')
													 ->where(['o.orderId'=>$orderId,'os.isClose'=>0])
													 ->value('sum(sg.goodsNum) osNum');
				$orders['canAfterSale'] = ($ogNum>$osNum);
			}
		}

		return $orders;
	}



	/**
	* 根据订单id获取 商品信息跟商品评价
	*/
	public function getOrderInfoAndAppr(){
		$orderId = (int)input('oId');
		$shopId = (int)session('WST_USER.shopId');

		$goodsInfo = Db::name('supplier_order_goods')
					->field('id,orderId,goodsName,goodsId,goodsSpecNames,goodsImg,goodsSpecId,goodsCode')
					->where(['orderId'=>$orderId])
					->select();
		//根据商品id 与 订单id 取评价
		$alreadys = 0;// 已评价商品数
		$count = count($goodsInfo);//订单下总商品数
		if($count>0){
			foreach($goodsInfo as $k=>$v){
				$goodsInfo[$k]['goodsSpecNames'] = str_replace('@@_@@', ';', $v['goodsSpecNames']);

				$appraise = Db::name('supplier_goods_appraises')
							->field('goodsScore,serviceScore,timeScore,content,images,createTime')
							->where(['goodsId'=>$v['goodsId'],
							         'goodsSpecId'=>$v['goodsSpecId'],
									 'orderId'=>$orderId,
									 'dataFlag'=>1,
									 'orderGoodsId'=>$v['id'],
									 ])->find();
				if(!empty($appraise)){
					++$alreadys;
					$appraise['images'] = ($appraise['images']!='')?explode(',', $appraise['images']):[];
				}
				$goodsInfo[$k]['appraise'] = $appraise;
			}
		}
		return ['count'=>$count,'data'=>$goodsInfo,'alreadys'=>$alreadys];

	}

    /**
     * 根据订单id,订单商品id获取单个商品信息
     */
    public function getOrderInfoByGoodsId($uId=0){
        $orderId = (int)input('oId');
        $orderGoodsId = (int)input('orderGoodsId');
        $goodsInfo = Db::name('supplier_order_goods')
            ->field('id,orderId,goodsName,goodsId,goodsSpecNames,goodsImg,goodsSpecId,goodsCode')
            ->where(['orderId'=>$orderId,'id'=>$orderGoodsId])
            ->find();
        if($goodsInfo){
            $goodsInfo['goodsSpecNames'] = str_replace('@@_@@', ';', $goodsInfo['goodsSpecNames']);
        }
        return $goodsInfo;
    }
	
	/**
	 * 检查订单是否已支付
	 */
	public function checkOrderPay (){
		$shopId = (int)session('WST_USER.shopId');
		$orderNo = input("orderNo");
		$isBatch = (int)input("isBatch");
		$rs = array();
		$where = ["shopId"=>$shopId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1];
		if($isBatch==1){
			$where['orderunique'] = $orderNo;
		}else{
			$where['orderNo'] = $orderNo;
		}
		$rs = $this->field('orderId,orderNo')->where($where)->select();
		if(count($rs)>0){
			return WSTReturn('',1);
		}else{
			return WSTReturn('订单已支付',-1);
		}
	}
	
	/**
	 * 检查订单是否已支付
	 */
	public function checkOrderPay2 ($obj){
		$shopId = $obj["shopId"];
		$orderNo = $obj["orderNo"];
		$isBatch = $obj["isBatch"];
		$rs = array();
		$where = ["shopId"=>$shopId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1];
		if($isBatch==1){
			$where['orderunique'] = $orderNo;
		}else{
			$where['orderNo'] = $orderNo;
		}
		$rs = $this->field('orderId,orderNo')->where($where)->select();
		if(count($rs)>0){
			return WSTReturn('',1);
		}else{
			return WSTReturn('订单已支付',-1);
		}
	}
	
	

	/**
	 * 完成支付订单
	 */
	public function complatePay ($obj){
		$trade_no = $obj["trade_no"];
		$isBatch = (int)$obj["isBatch"];
		$orderNo = $obj["out_trade_no"];
		$userId = (int)$obj["userId"];
		$shopId = (int)$obj["shopId"];
		$payFrom = $obj["payFrom"];
		$payMoney = (float)$obj["total_fee"];
		if($payFrom!=''){
			$cnt = model('supplier_orders')
			->where(['payFrom'=>$payFrom,"userId"=>$userId,"tradeNo"=>$trade_no])
			->count();
			if($cnt>0){
				return WSTReturn('订单已支付',-1);
			}
		}
        $where = [["shopId","=",$shopId],["dataFlag","=",1],["orderStatus","=",-2],["isPay","=",0],["payType","=",1]];
		$where[] = ["needPay",">",0];
		if($isBatch==1){
			$where[] = ['orderunique',"=",$orderNo];
		}else{
			$where[] = ['orderNo',"=",$orderNo];
		}
		$orders = model('supplier_orders')->where($where)->field('needPay,orderId,orderType,orderNo,supplierId,payFrom,realTotalMoney,deliverType,userPhone')->select();

	    if(count($orders)==0)return WSTReturn('无效的订单信息',-1);
		$needPay = 0;
	    foreach ($orders as $key => $v) {
	    	$needPay += $v['needPay'];
	    }
		if($needPay>$payMoney){
			return WSTReturn('支付金额不正确',-1);
		}
		Db::startTrans();
		try{
			$data = array();
			$data["needPay"] = 0;
			$data["isPay"] = 1;
			$data["orderStatus"] = 0;
			$data["tradeNo"] = $trade_no;
			$data["payFrom"] = $payFrom;
			$data["payTime"] = date("Y-m-d H:i:s");
			$data["isBatch"] = $isBatch;
			$data["totalPayFee"] = $payMoney*100;
			$rs = model('supplier_orders')->where($where)->update($data);
	
			if($needPay>0 && false != $rs){
				foreach ($orders as $key =>$v){
					$orderId = $v["orderId"];
					$supplier = model('suppliers')->get($v->supplierId);
					if($v['deliverType']==1){
						$verificationCode = WSTOrderVerificationCode($v->supplierId,1);
						model('supplier_orders')->where(['orderId'=>$orderId])->update(["verificationCode"=>$verificationCode]);

						if($deliverType==1){//自提
							//自提订单（已支付）发送核验码
							$tpl = WSTMsgTemplates('PHONE_USER_ORDER_VERIFICATCODE');
					        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
					        	$userPhone = $v['userPhone'];
					        	$supplierName = $supplier["supplierName"];
					        	$supplierAddress = $supplier["supplierAddress"];
					        	$splieVerificationCode = join(" ",str_split($verificationCode,4));
					            $params = ['tpl'=>$tpl,'params'=>['MALL_NAME'=>WSTConf('CONF.mallName'),'ORDER_CODE'=>$splieVerificationCode,'SHOP_NAME'=>$storeName,'SHOP_ADDRESS'=>$supplierName,'SHOP_ADDRESS'=>$supplierAddress]];
					            model("common/LogSms")->sendSMS(0,$userPhone,$params,'complatePay','',$userId,0);
					        }
					    }
					}
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 0;
					$logOrder['logContent'] = "订单已支付,下单成功";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('supplier_log_orders')->insert($logOrder);
					//创建一条充值流水记录
					$lm = [];
					$lm['targetType'] = 1;
					$lm['targetId'] = $shopId;
					$lm['dataId'] = $orderId;
					$lm['dataSrc'] = 1;
					$lm['remark'] = '交易订单【'.$v['orderNo'].'】充值¥'.$needPay;
					$lm['moneyType'] = 1;
					$lm['money'] = $needPay;
					$lm['payType'] = $payFrom;
					$lm['tradeNo'] = $trade_no;
					$lm['createTime'] = date('Y-m-d H:i:s');
					model('LogMoneys')->create($lm);
					//创建一条支出流水记录
					$lm = [];
					$lm['targetType'] = 1;
					$lm['targetId'] = $shopId;
					$lm['dataId'] = $orderId;
					$lm['dataSrc'] = 1;
					$lm['remark'] = '交易订单【'.$v['orderNo'].'】支出¥'.$needPay;
					$lm['moneyType'] = 0;
					$lm['money'] = $needPay;
					$lm['payType'] = 0;
					$lm['createTime'] = date('Y-m-d H:i:s');
					model('LogMoneys')->create($lm);
					
					//发送一条商家信息
					$tpl = WSTMsgTemplates('ORDER_HASPAY');
			        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
			            $find = ['${ORDER_NO}'];
			            $replace = [$v['orderNo']];
			            
			            $msg = array();
			            $msg["supplierId"] = $supplier["supplierId"];
			            $msg["tplCode"] = $tpl["tplCode"];
			            $msg["msgType"] = 1;
			            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
			            $msg["msgJson"] = ['from'=>1,'dataId'=>$orderId];
			            model("common/SupplierMessageQueues")->add($msg);
			        }

				}
			}else{
				$data = array();
				$data["shopMoney"] = Db::raw("shopMoney+".$payMoney);
				Db::name('shops')->where("shopId",$shopId)->update($data);
				//创建一条充值流水记录
				$lm = [];
				$lm['targetType'] = 1;
				$lm['targetId'] = $shopId;
				$lm['dataId'] = $orderNo;
				$lm['dataSrc'] = 1;
				$lm['remark'] = '交易订单充值¥'.$payMoney;
				$lm['moneyType'] = 1;
				$lm['money'] = $payMoney;
				$lm['payType'] = $payFrom;
				$lm['tradeNo'] = $trade_no;
				$lm['createTime'] = date('Y-m-d H:i:s');
				model('LogMoneys')->create($lm);
			}
			Db::commit();
			return WSTReturn('支付成功',1);
		}catch (\Exception $e) {
			Db::rollback();
			return WSTReturn('操作失败',-1);
		}
	}
	
	/**
	 * 获取支付订单信息
	 */
	public function getPayOrders ($obj){
		$shopId = (int)$obj["shopId"];
		$orderNo = $obj["orderNo"];
		$isBatch = (int)$obj["isBatch"];
		$needPay = 0;
		$where = [["shopId",'=',$shopId],
				  ["dataFlag",'=',1],
				  ["orderStatus",'=',-2],
				  ["isPay",'=',0],
				  ["payType",'=',1],
				  ["needPay",'>', 0],
				  [($isBatch==1)?'orderunique':'orderNo','=',$orderNo]];
		$data = array();
		$needPay = model('supplier_orders')->where($where)->sum('needPay');
		$payRand = model('supplier_orders')->where($where)->max('payRand');
		$data["needPay"] = $needPay;
		$data["payRand"] = $payRand;
		return $data;
	}
	
	/**
	 * 导出订单
	 */
	public function toExport(){
		$name='order';
		$where = ['o.dataFlag'=>1];
		$orderStatus = (int)input('orderStatus',0);
		if($orderStatus==0){
			$name='PendingDelOrder';
		}else if($orderStatus==-2){
			$name='PendingPayorder';
		}else if($orderStatus==1){
			$name='DistributionOrder';
		}else if($orderStatus==-1){
			$name='CancelOrder';
		}else if($orderStatus==-3){
			$name='RejectionOrder';
		}else if($orderStatus==2){
			$name='ReceivedOrder';
		}else if($orderStatus==10000){
			$name='CancelOrder/RejectionOrder';
		}else if($orderStatus==20000){
			$name='PendingRecOrder';
		}
		$name = $name.date('Ymd');
		$shopId = (int)session('WST_USER.shopId');
		$where = [];
		$where[] = ['o.shopId','=',$shopId];
		$orderNo = input('orderNo');
		$supplierName = input('supplierName');
		
		$type = (int)input('type',-1);
		$payType = $type>0?$type:(int)input('payType',-1);
		$deliverType = (int)input('deliverType');
		if($orderStatus == 10000)$orderStatus = [-1,-3];
		if($orderStatus == 20000)$orderStatus = [0,1];
		if(is_array($orderStatus)){
			$where[] = ['o.orderStatus','in',$orderStatus];
		}else{
			$where[] = ['o.orderStatus','=',$orderStatus];
		}
		if($orderNo!=''){
			$where[] = ['orderNo','like',"%$orderNo%"];
		}
		if($supplierName!=''){
			$where[] = ['supplierName','like',"%$supplierName%"];
		}
		if($payType > -1){
			$where[] =  ['payType','=',$payType];
		}
		if($deliverType > -1){
			$where[] =  ['deliverType','=',$deliverType];
		}
		$page = $this->alias('o')->where($where)->join('suppliers s','o.supplierId=s.supplierId','left')
		->join('supplier_order_refunds orf','orf.orderId=o.orderId and refundStatus=0','left')
		->join('supplier_log_orders lo','lo.orderId=o.orderId and lo.orderStatus in (-1,-3) ','left')
		->field('o.orderId,orderNo,goodsMoney,totalMoney,realTotalMoney,o.orderStatus,deliverType,deliverMoney,isAppraise,o.deliverMoney,lo.logContent,o.payTime,o.payFrom
		,o.invoiceJson,o.isMakeInvoice,o.isInvoice,o.isRefund,payType,o.userName,o.userAddress,o.userPhone,o.orderRemarks,o.invoiceClient,o.receiveTime,o.deliveryTime,orderSrc,o.createTime,orf.id refundId,s.areaId supplierAreaId,s.supplierAddress')
		->order('o.createTime', 'desc')
		->select();
		if(count($page)>0){
			foreach ($page as $v){
				$orderIds[] = $v['orderId'];
			}
			$goods = Db::name('supplier_order_goods')->where([['orderId','in',$orderIds]])->select();
			$goodsMap = [];
			foreach ($goods as $v){
				$v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
				$goodsMap[$v['orderId']][] = $v;
			}
			foreach ($page as $key => $v){
				$page[$key]['invoiceArr'] = '';
				if($v['isInvoice']==1){
					$invoiceArr = json_decode($v['invoiceJson'],true);
					$page[$key]['invoiceArr'] = " ".$invoiceArr['invoiceHead'];
					if(isset($invoiceArr['invoiceCode'])){
						$page[$key]['invoiceArr'] = " ".$invoiceArr['invoiceHead'].'|'.$invoiceArr['invoiceCode'];
					}
				}
				$page[$key]['supplierAddr'] = model('common/areas')->getParentNames($v['supplierAreaId']);
		        $page[$key]['supplierAddress'] = implode('',$v['supplierAddr']).$v['supplierAddress'];
		        if($page[$key]['deliverType']==1)$page[$key]['userAddress'] = $page[$key]['supplierAddress'];
				$page[$key]['payTypeName'] = WSTLangPayType($v['payType']);
				$page[$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
				$page[$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
				$page[$key]['supplier_goods'] = $goodsMap[$v['orderId']];
                $page[$key]['isMakeInvoice'] = ($v['isMakeInvoice']==1)?'已开':'未开';
			}
		}
		require Env::get('root_path') . 'extend/phpexcel/PHPExcel.php';
		
		$objPHPExcel = new \PHPExcel();
		// 设置excel文档的属性
		$objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
		->setLastModifiedBy("WSTMart")//最后修改人
		->setTitle($name)//标题
		->setSubject($name)//题目
		->setDescription($name)//描述
		->setKeywords("订单")//关键字
		->setCategory("Test result file");//种类
	
		// 开始操作excel表
		$objPHPExcel->setActiveSheetIndex(0);
		// 设置工作薄名称
		$objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
		// 设置默认字体和大小
		$objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
		$styleArray = array(
				'font' => array(
						'bold' => true,
						'color'=>array(
								'argb' => 'ffffffff',
						)
				)
		);
		
		//设置宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
		$objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:W1');
		$objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$objRow->getFill()->getStartColor()->setRGB('666699');
		$objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '订单编号')->setCellValue('B1', '订单状态')->setCellValue('C1', '收货人')->setCellValue('D1', '收货地址')->setCellValue('E1', '联系方式')
		->setCellValue('F1', '支付方式')->setCellValue('G1', '支付来源')->setCellValue('H1', '配送方式')->setCellValue('I1', '发票状态')->setCellValue('W1', '买家留言')->setCellValue('J1', '发票信息')
		->setCellValue('K1', '订单商品')->setCellValue('L1', '商品价格')->setCellValue('M1', '数量')->setCellValue('N1', '订单总金额')->setCellValue('O1', '运费')->setCellValue('P1', '实付金额')
		->setCellValue('Q1', '下单时间')->setCellValue('R1', '付款时间')->setCellValue('S1', '发货时间')->setCellValue('T1', '收货时间')->setCellValue('U1', '取消/拒收原因')->setCellValue('V1', '是否退款');
		$objPHPExcel->getActiveSheet()->getStyle('A1:W1')->applyFromArray($styleArray);
		$i = 1;
		$totalRow = 0;
		for ($row = 0; $row < count($page); $row++){
			$goodsn = count($page[$row]['supplier_goods']);
			$i = $i+1;
			$i2 = $i3 = $i;
			$i = $i+(1*$goodsn)-1;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i2.':A'.$i)->mergeCells('B'.$i2.':B'.$i)->mergeCells('C'.$i2.':C'.$i)->mergeCells('D'.$i2.':D'.$i)->mergeCells('E'.$i2.':E'.$i)->mergeCells('F'.$i2.':F'.$i)
			->mergeCells('G'.$i2.':G'.$i)->mergeCells('H'.$i2.':H'.$i)->mergeCells('I'.$i2.':I'.$i)->mergeCells('J'.$i2.':J'.$i)->mergeCells('N'.$i2.':N'.$i)->mergeCells('O'.$i2.':O'.$i)
			->mergeCells('P'.$i2.':P'.$i)->mergeCells('Q'.$i2.':Q'.$i)->mergeCells('R'.$i2.':R'.$i)->mergeCells('S'.$i2.':S'.$i)->mergeCells('T'.$i2.':T'.$i)->mergeCells('U'.$i2.':U'.$i)->mergeCells('V'.$i2.':V'.$i)->mergeCells('W'.$i2.':W'.$i);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, $page[$row]['orderNo'])->setCellValue('B'.$i2, $page[$row]['status'])->setCellValue('C'.$i2, $page[$row]['userName'])->setCellValue('D'.$i2, $page[$row]['userAddress'])
			->setCellValue('E'.$i2, $page[$row]['userPhone'])->setCellValue('F'.$i2, $page[$row]['payTypeName'])->setCellValue('G'.$i2, ($page[$row]['payFrom'])?WSTLangPayFrom($page[$row]['payFrom']):'')->setCellValue('H'.$i2, $page[$row]['deliverType'])
			->setCellValue('I'.$i2, $page[$row]['isMakeInvoice'])->setCellValue('W'.$i2, $page[$row]['orderRemarks'])->setCellValue('J'.$i2, $page[$row]['invoiceArr'])->setCellValue('N'.$i2, $page[$row]['totalMoney'])->setCellValue('O'.$i2, $page[$row]['deliverMoney'])->setCellValue('P'.$i2, $page[$row]['realTotalMoney'])
			->setCellValue('Q'.$i2, $page[$row]['createTime'])->setCellValue('R'.$i2, $page[$row]['payTime'])->setCellValue('S'.$i2, $page[$row]['deliveryTime'])->setCellValue('T'.$i2, $page[$row]['receiveTime'])
			->setCellValue('U'.$i2, $page[$row]['logContent'])->setCellValue('V'.$i2, ($page[$row]['isRefund']==1)?'是':'');
			$objPHPExcel->getActiveSheet()->getStyle('D'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('U'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			for ($row2 = 0; $row2 < $goodsn; $row2++){
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i3, (($page[$row]['supplier_goods'][$row2]['goodsCode']=='gift')?'【赠品】':'').$page[$row]['supplier_goods'][$row2]['goodsName'].(($page[$row]['supplier_goods'][$row2]['goodsSpecNames']!='')?'【'.$page[$row]['supplier_goods'][$row2]['goodsSpecNames'].'】':''))->setCellValue('L'.$i3, $page[$row]['supplier_goods'][$row2]['goodsPrice'])->setCellValue('M'.$i3, $page[$row]['supplier_goods'][$row2]['goodsNum']);
				$objPHPExcel->getActiveSheet()->getStyle('K'.$i3)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$i3 = $i3 + 1;
			}
			$totalRow = $i3;
		}
	    $totalRow = ($totalRow==0)?1:$totalRow-1;
	    $objPHPExcel->getActiveSheet()->getStyle('A1:W'.$totalRow)->applyFromArray(array(
				'borders' => array (
						'allborders' => array (
								'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
								'color' => array ('argb' => 'FF000000'),     //设置border颜色
						)
				)
		));
		$this->PHPExcelWriter($objPHPExcel,$name);
	}
	
	
	public function addPayLog($txt){
		$logOrder = [];
		$logOrder['txt'] = $txt;
		$logOrder['logTime'] = date('Y-m-d H:i:s');
		Db::name('pay_log')->insert($logOrder);
	}

	/**
	 * 余额支付
	 */
	public function payByWallet(){
		$payPwd = input('payPwd');
		if(!$payPwd)return WSTReturn('请输入密码',-1);
		
		$decrypt_data = WSTRSA($payPwd);
		if($decrypt_data['status']==1){
			$payPwd = $decrypt_data['data'];
		}else{
			return WSTReturn('支付失败');
		}
		
        $pkey = input('pkey');
        $pkey = WSTBase64urlDecode($pkey);
        $pkey = explode('@',$pkey);
        if(count($pkey)>1){
        	$orderNo = $pkey[0];
        	$isBatch = (int)$pkey[1];
        }else{
        	$orderNo = input('orderNo');
        	$isBatch = (int)input('isBatch');
        }
        $userId = (int)session('WST_USER.userId');
        $shopId = (int)session('WST_USER.shopId');
        //判断是否开启余额支付
        $isEnbalePay = model('Payments')->isEnablePayment('wallets');
        if($isEnbalePay==0)return WSTReturn('非法的支付方式',-1);
        //判断订单状态
        $where = ["shopId"=>$shopId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1];
		if($isBatch==1){
			$where['orderunique'] = $orderNo;
		}else{
			$where['orderNo'] = $orderNo;
		}
		$orders = $this->field('orderId,orderNo,orderType,needPay,supplierId,payFrom,realTotalMoney,deliverType,userPhone')->where($where)->select();
		if(count($orders)==0)return WSTReturn('您的订单已支付',-1);
		//判断订单金额是否正确
		$needPay = 0;
		foreach ($orders as $v) {
			$needPay += $v->needPay;
		}
	    //获取用户钱包
	    $user = model('users')->get(['userId'=>$userId]);
	    $shop = model('shops')->get(['shopId'=>$shopId]);
	    if($user->payPwd=='')return WSTReturn('您未设置支付密码，请先设置密码',-1);
	    if($user->payPwd!=md5($payPwd.$user->loginSecret))return WSTReturn('您的支付密码不正确',-1);
		if($needPay > $shop->shopMoney)return WSTReturn('您的钱包余额不足',-1);
		
		$rechargeMoney = $shop->rechargeMoney;
		Db::startTrans();
		try{
            //循环处理每个订单
            foreach ($orders as $order) {
            	//处理订单信息
            	$tmpNeedPay = $order->needPay;
            	$lockCashMoney = ($rechargeMoney>$tmpNeedPay)?$tmpNeedPay:$rechargeMoney;
            	$order->needPay = 0;
            	$order->isPay = 1;
            	$order->payTime = date('Y-m-d H:i:s');
            	$order->orderStatus = 0;
            	$order->payFrom = 'wallets';
            	$order->lockCashMoney = $lockCashMoney;
            	if($order->deliverType==1)$order->verificationCode = WSTOrderVerificationCode($order->supplierId,1);
            	$result = $order->save();
                if(false != $result){
                
                	if($order['deliverType']==1){
						//自提订单（已支付）发送核验码
						$tpl = WSTMsgTemplates('PHONE_USER_ORDER_VERIFICATCODE');
				        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
				        	$user = Db::name("users")->where(['userId'=>$userId])->field("userPhone")->find();
				        	$userPhone = $order->userPhone;
				        	$supplierId = $order->supplierId;
				        	$supplier = Db::name("suppliers")->where(["supplierId"=>$supplierId])->field("supplierName,supplierAddress")->find();
				        	$supplierName = $supplier["supplierName"];
					    	$supplierAddress = $supplier["supplierAddress"];
					    	$splieVerificationCode = join(" ",str_split($order->verificationCode,4));
				            $params = ['tpl'=>$tpl,'params'=>['MALL_NAME'=>WSTConf('CONF.mallName'),'ORDER_CODE'=>$splieVerificationCode,'SHOP_NAME'=>$supplierName,'SHOP_ADDRESS'=>$supplierAddress]];
				            model("common/LogSms")->sendSMS(0,$userPhone,$params,'payByWallet','',$userId,0);
				        }
					}

					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $order->orderId;
					$logOrder['orderStatus'] = 0;
					$logOrder['logContent'] = "订单已支付,下单成功";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('supplier_log_orders')->insert($logOrder);

                    //创建一条支出流水记录
					$lm = [];
					$lm['targetType'] = 1;
					$lm['targetId'] = $shopId;
					$lm['dataId'] = $order->orderId;
					$lm['dataSrc'] = 1;
					$lm['remark'] = '交易订单【'.$order->orderNo.'】支出¥'.$tmpNeedPay;
					$lm['moneyType'] = 0;
					$lm['money'] = $tmpNeedPay;
					$lm['payType'] = 'wallets';
					model('LogMoneys')->add($lm);
					//修改用户充值金额
					model('shops')->where(["shopId"=>$shopId])->setDec("rechargeMoney",$lockCashMoney);
					
                    //发送一条商家信息
					$tpl = WSTMsgTemplates('ORDER_HASPAY');
			        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
			            $find = ['${ORDER_NO}'];
			            $replace = [$order->orderNo];
			            $msg = array();
			            $msg["supplierId"] = $order->supplierId;
			            $msg["tplCode"] = $tpl["tplCode"];
			            $msg["msgType"] = 1;
			            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
			            $msg["msgJson"] = ['from'=>1,'dataId'=>$order->orderId];
			            model("common/SupplierMessageQueues")->add($msg);
			        } 
					

                }
            }
			Db::commit();
			return WSTReturn('订单支付成功',1);
		}catch (\Exception $e) {
			//print_r($e);
			Db::rollback();
			return WSTReturn('订单支付失败');
		}
	}

	/**
	 * 获取订单金额以及用户钱包金额
	 */
	public function getOrderPayInfo($obj){
        $shopId = (int)$obj["shopId"];
		$orderNo = $obj["orderNo"];
		$isBatch = (int)$obj["isBatch"];
		$needPay = 0;
		$where = ["shopId"=>$shopId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1];
		$condition[] = ["needPay",">",0];
		if($isBatch==1){
			$where['orderunique'] = $orderNo;
		}else{
			$where['orderNo'] = $orderNo;
		}
		$orders = $this->where($where)->where($condition)->field('needPay,payRand')->select();
		if(empty($orders))return [];
		$needPay = 0;
		$payRand = 0;
		foreach($orders as $order){
            $needPay += $order['needPay'];
            if($payRand<$order['payRand'])$payRand = $order['payRand'];
		}
		$data = array();
		$data["needPay"] = $needPay;
		$data["payRand"] = $payRand;
		return $data;
	}
	
	public function getOrderPayFrom($out_trade_no){
		$rs = $this->where(['dataFlag'=>1,'orderNo|orderunique'=>$out_trade_no])->field('orderId,userId,orderNo,orderunique')->find();
		if(!empty($rs)){
			$rs['isBatch'] = ($rs['orderunique'] == $out_trade_no)?1:0;
		}
		return $rs;
	}
	/**
	* 用户-提醒发货
	*/
	public function noticeDeliver($uId=0){
		$orderId = (int)input('id');
		$shopId = (int)session('WST_USER.shopId');
		Db::startTrans();
		try{
			$rs = $this->where(['shopId'=>$shopId,'orderId'=>$orderId])->setField('noticeDeliver',1);
			if($rs!==false){
				$info = $this->alias('o')->field('supplierId,orderNo')->where(['shopId'=>$shopId,'orderId'=>$orderId])->find();
				//发送商城消息提醒卖家
				$tpl = WSTMsgTemplates('ORDER_REMINDER');
                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
                    $find = ['${LOGIN_NAME}','${ORDER_NO}'];
                    $replace = [session('WST_USER.loginName'),$info['orderNo']];
                    
                    $msg = array();
		            $msg["supplierId"] = $info['supplierId'];
		            $msg["tplCode"] = $tpl["tplCode"];
		            $msg["msgType"] = 1;
		            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
		            $msg["msgJson"] = [];
		            model("common/SupplierMessageQueues")->add($msg);
                }
			}
			Db::commit();
			return WSTReturn('提醒成功',1);
		}catch(\Exception $e){
			Db::rollback();
		}
		return WSTReturn('提醒失败',-1);
	}

    /**
     * 获取单条订单的商品信息
     */
    public function waitDeliverById(){
        $orderId = (int)input('id');
        $goods = Db::name('supplier_order_goods')->where('orderId','=',$orderId)->select();
        $order = Db::name('supplier_orders')->field('deliverType,userAddress,userName,userPhone')->where('orderId','=',$orderId)->find();
        $orderExpressGoodsIds = Db::name('supplier_order_express')->field('orderGoodsId')->where(['orderId'=>$orderId])->select();
        $deliveredGoodsIds = [];
        foreach($orderExpressGoodsIds as $k => $v){
            $temp = explode(',',$v['orderGoodsId']);
            $deliveredGoodsIds = array_merge($deliveredGoodsIds,$temp);
        }
        $data = [];
        $data['list'] = [];
        $data['userName'] = $order['userName'];
        $data['userPhone'] = $order['userPhone'];
        $data['userAddress'] = $order['userAddress'];
        $data['deliverType'] = $order['deliverType'];
        if($goods){
            foreach($goods as $k => $v){
                $goods[$k]['hasDeliver'] = (in_array($v['id'],$deliveredGoodsIds))?true:false;
            }
            $data['list'] = $goods;
        }
        return $data;
    }

    /**
	 * 根据送货城市获取运费
	 * @param $cityId 送货城市Id
	 * @param $supplierId 店铺ID
	 * @param $carts 购物车信息
	 */
	public function getOrderFreight($supplierId,$cityId,$carts=[]){
	    $cnt = Db::name("supplier_express")->where(["supplierId"=>$supplierId,"dataFlag"=>1,"isEnable"=>1])->count();
	    $freight = 0;
	    if($cnt>0){
	        $freight = model("supplierCarts")->getSupplierFreight($supplierId,$cityId,$carts);
	    }
	    return ($freight>0)?$freight:0;
	}
}
