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
 * 退款订单业务处理类
 */
class SupplierOrderRefunds extends Base{
	
    /**
	 * 获取用户退款订单列表
	 */
	public function refundPageQuery(){
		$startDate = input('startDate');
		$endDate = input('endDate');
		$where = [];
		$where[] = ['o.dataFlag','=',1];
		$where[] = ['orderStatus','in',[-1,-3]];
		$orderNo = input('orderNo');
		$supplierName = input('supplierName');
		$deliverType = (int)input('deliverType',-1);
		$areaId1 = (int)input('areaId1');
		if($areaId1>0){
			$where[] = ['s.areaIdPath','like',"$areaId1%"];
			$areaId2 = (int)input("areaId1_".$areaId1);
			if($areaId2>0)$where[] = ['s.areaIdPath','like',$areaId1."_"."$areaId2%"];
			$areaId3 = (int)input("areaId1_".$areaId1."_".$areaId2);
			if($areaId3>0)$where[] = ['s.areaId','=',$areaId3];
		}
		$isRefund = (int)input('isRefund',-1);
		if($orderNo!='')$where[] = ['orderNo','like','%'.$orderNo.'%'];
		if($supplierName!='')$where[] = ['supplierName|supplierSn','like','%'.$supplierName.'%'];
		
		if($deliverType!=-1)$where[] = ['o.deliverType','=',$deliverType];
		if($isRefund!=-1)$where[] = ['o.isRefund','=',$isRefund];

		if($startDate!='' && $endDate!=''){
			$where[] = ['orf.createTime','between',[$startDate.' 00:00:00',$endDate.' 23:59:59']];
		}else if($startDate!=''){
			$where[] = ['orf.createTime','>=',$startDate.' 00:00:00'];
		}else if($endDate!=''){
			$where[] = ['orf.createTime','<=',$endDate.' 23:59:59'];
		}

		// 排序
		$sort = input('sort');
		$sort = str_replace('orderCodeTitle','orderCode',$sort);
		$order = [];
		if($sort!=''){
			$sortArr = explode('.',$sort);
			$order[$sortArr[0]] = $sortArr[1];
		}
		$page = Db::name('supplier_orders')->alias('o')->join('__SUPPLIERS__ s','o.supplierId=s.supplierId','left')
		     ->join('__USERS__ u','o.userId=u.userId','left')
		     ->join('__SUPPLIER_ORDER_REFUNDS__ orf ','o.orderId=orf.orderId and refundStatus in (1,2)') 
			 ->where($where)
			 ->whereOr([['serviceId','<>',0]])
		     ->field('orf.isServiceRefund,orf.serviceId,orf.id refundId,o.orderId,o.orderNo,s.supplierName,s.supplierId,s.supplierQQ,s.supplierWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,u.loginName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,orf.backMoney,orf.refundRemark,o.isRefund,orf.createTime,o.orderCode')
			 ->order($order)
			 ->order('orf.createTime', 'desc')
			 ->paginate(input('limit/d'))->toArray();
	    if(count($page['data'])>0){
	    	 foreach ($page['data'] as $key => $v){
	    	 	 $page['data'][$key]['payType'] = WSTLangPayType($v['payType']);
	    	 	 $page['data'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['data'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
				 $page['data'][$key]['orderCodeTitle'] = WSTOrderModule($v['orderCode']);
				 $page['data'][$key]['refundTo'] = WSTLangPayFrom($v['payFrom']);
				
	    	 }
	    }
	    return $page;
	}
	/**
	 * 获取退款资料
	 */
	public function getInfoByRefund(){
		$where = [['orf.id','=',(int)input('get.id')],
				  ['isRefund','=',0],
				  ['orderStatus','in',[-1,-3]],
				  ['refundStatus','=',1]];
		$serviceId = (int)input('serviceId');
		if($serviceId>0){
			$where = [
				'serviceId'=>$serviceId,
				'isRefund'=>0,
				'orf.id'=>(int)input('get.id'),
				'orderStatus'=>2,
				'refundStatus'=>1
			];
		}
		$rs = $this->alias('orf')->join('__SUPPLIER_ORDERS__ o','orf.orderId=o.orderId')
				 ->where($where)
		         ->field('orf.id refundId,orderNo,o.orderId,goodsMoney,refundReson,refundOtherReson,totalMoney,realTotalMoney,deliverMoney,payType,payFrom,backMoney,tradeNo')
				 ->find();
		if($serviceId>0){
			$rs['serviceId'] = $serviceId;
			// 替换数据
			$osData = Db::name('supplier_order_services')
					->field('refundableMoney')
					->where(['id'=>$serviceId])
					->find();
			// 售后单总金额 = 售后单可退款金额
			$rs['totalMoney'] = $osData['refundableMoney'];
		}
		return $rs;
	}

	/**
	 * 退款
	 */
	public function orderRefund(){
		$id = (int)input('post.id');
		if($id==0)return WSTReturn("操作失败!");
		$refund = $this->get($id);
		if(empty($refund) || $refund->refundStatus!=1)return WSTReturn("该退款订单不存在或已退款!");
		$rs = array();
        $order = model('admin/SupplierOrders')->get($refund->orderId);
        if($order->payType==1 && $order->payFrom=='wallets'){
        	$rs = $this->saveOrderRefund($refund,$order);
        }else if($order->payType==1 && $order->payFrom=='weixinpays'){
        	$wm = model("admin/SupplierWeixinpays");
        	$rs = $wm->orderRefund($refund,$order);
        }else if($order->payType==1 && $order->payFrom=='alipays'){
        	$am = model("admin/SupplierAlipays");
        	$rs = $am->orderRefund($refund,$order);
        }else{
        	$rs = $this->saveOrderRefund($refund,$order);
        }
        return $rs;
	}

	public function complateOrderRefund($obj){
		Db::startTrans();
        try{
			$content = $obj['content'];
			$refundTradeNo = $obj['refundTradeNo'];
			$refundId = $obj['refundId'];
			$refund = $this->get($refundId);
			$order = model('admin/SupplierOrders')->get($refund->orderId);
			$isOrderService = !!($refund->serviceId>0);
			if(!((in_array($order->orderStatus,[-1,-3]) || ($order->orderStatus==2 && $isOrderService))
				&& 
				$order->isRefund==0 
				&& 
				($order->isPay==1 || ($order->payType==0))
				))return WSTReturn("无效的退款订单!");
			//修改退款单信息
			$refund->refundRemark = $content;
			$refund->refundTime = date('Y-m-d H:i:s');
			$refund->refundStatus = 2;
			if($isOrderService){
				// 标记为已退款
				$refund->isServiceRefund = 1;
			}
			$refund->refundTradeNo = $refundTradeNo;
			$refund->save();
			//修改订单状态
			// 需要判断是否确人收货之后，所有的商品都进行的退款
			if($isOrderService){
				// 协商历史，记录退款详情
				$log = [
                    'logTime'=>date('Y-m-d H:i:s'),
                    'orderId'=>$order->orderId,
                    'serviceId'=>$refund->serviceId,
                    'logTargetId'=>0,
                    'logType'=>2,
                    'logContent'=>"管理员打款给用户".$refund->backMoney."元"
                ];
				Db::name('supplier_log_services')->insert($log);
				// 售后单总金额及总积分、使用积分数、积分抵扣金额
				$osData = Db::name('supplier_order_services')->where(['id'=>$refund->serviceId])->find();
				$goodsMoney = $osData['refundableMoney'];
				// 将售后单状态改为已完成退款
				Db::name('supplier_order_services')->where(['id'=>$refund->serviceId])->setField('serviceStatus',5);
			}else{

				//修改订单状态
				$order->isRefund = 1;
				$order->isClosed = 1;
			}
			$order->save();	
			
			//发送一条用户信息
			$tpl = WSTMsgTemplates('ORDER_REFUND_SUCCESS');
			if( $tpl['tplContent']!='' && $tpl['status']=='1'){
				$find = ['${ORDER_NO}','${REMARK}'];
				$replace = [$order->orderNo,$content];
				WSTSendMsg($order->userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>1,'dataId'=>$order->orderId]);
			} 
			//微信消息
			if((int)WSTConf('CONF.wxenabled')==1){
				$reasonData = WSTDatas('REFUND_TYPE',$refund->refundReson);
				$params = [];
				$params['ORDER_NO'] = $order->orderNo;
				$params['REASON'] = $reasonData['dataName'].(($refund->refundReson==10000)?" - ".$refund->refundOtherReson:"");           
				$params['MONEY'] = $refund->backMoney;
				WSTWxMessage(['CODE'=>'WX_ORDER_REFUND_SUCCESS','userId'=>$order->userId,'params'=>$params]);
			}
			//如果有钱剩下，那么就退回到供货商钱包
			$supplierMoneys = $order->realTotalMoney-$refund->backMoney;
			
			if(!$isOrderService && $supplierMoneys>0){
				//创建供货商资金流水
				$lm = [];
				$lm['targetType'] = 1;
				$lm['targetId'] = $order->supplierId;
				$lm['dataId'] = $order->orderId;
				$lm['dataSrc'] = 1;
				$lm['remark'] = '订单【'.$order->orderNo.'】退款，返回供货商金额¥'.$supplierMoneys."。";
				$lm['moneyType'] = 1;
				$lm['money'] = $supplierMoneys;
				$lm['payType'] = 0;
				$lm['createTime'] = date('Y-m-d H:i:s');
				model('LogMoneys')->add($lm);
				
			}
			Db::commit();
			return WSTReturn("退款成功",1); 
		}catch (\Exception $e) {
            Db::rollback();
        }
		return WSTReturn("退款失败，请刷新后再重试"); 
	}

	/**
	 * 退款
	 */
	public function saveOrderRefund($refund,$order){
		$content = input('post.content');
		$isOrderService = !!($refund->serviceId>0);
		$lockCashMoney = $order["lockCashMoney"];
		if(!((in_array($order->orderStatus,[-1,-3]) || ($order->orderStatus==2 && $isOrderService))
				&& 
				$order->isRefund==0 
				&& 
				($order->isPay==1)
			))return WSTReturn("无效的退款订单!");
		Db::startTrans();
        try{
        	//修改退款单信息
			$refund->refundRemark = $content;
			$refund->refundTime = date('Y-m-d H:i:s');
			$refund->refundStatus = 2;
			if($isOrderService){
				// 标记为已退款
				$refund->isServiceRefund = 1;
			}
			$refund->save();

			// 需要判断是否确人收货之后，所有的商品都进行的退款
			if($isOrderService){
				// 协商历史，记录退款详情
				$log = [
                    'logTime'=>date('Y-m-d H:i:s'),
                    'orderId'=>$order->orderId,
                    'serviceId'=>$refund->serviceId,
                    'logTargetId'=>0,
                    'logType'=>2,
                    'logContent'=>"管理员打款给用户".$refund->backMoney."元"
                ];
                Db::name('supplier_log_services')->insert($log);
				// 售后单总金额及总积分、使用积分数、积分抵扣金额
				$osData = Db::name('supplier_order_services')->where(['id'=>$refund->serviceId])->find();
				$goodsMoney = $osData['refundableMoney'];
				// 将售后单状态改为已完成退款
				Db::name('supplier_order_services')->where(['id'=>$refund->serviceId])->setField('serviceStatus',5);

			}else{

				//修改订单状态
				$order->isRefund = 1;
				$order->isClosed = 1;
			}
			$order->save();	
			//创建用户资金流水记录
			if($refund->backMoney>0){
				$lm = [];
				$lm['targetType'] = 1;
				$lm['targetId'] = $order->shopId;
				$lm['dataId'] = $order->orderId;
				$lm['dataSrc'] = 1;
				$lm['remark'] = '订单【'.$order->orderNo.'】退款¥'.$refund->backMoney."。".(($content!='')?"【退款备注】：".$content:'');
				$lm['moneyType'] = 1;
				$lm['money'] = $refund->backMoney;
				$lm['payType'] = 0;
				$lm['createTime'] = date('Y-m-d H:i:s');
				model('common/LogMoneys')->add($lm);
				//修改店铺充值金额
				model('shops')->where(["shopId"=>$order->shopId])->setInc("rechargeMoney",($lockCashMoney>$refund->backMoney)?$refund->backMoney:$lockCashMoney);
			}
			
			
			//发送一条用户信息
			$tpl = WSTMsgTemplates('ORDER_REFUND_SUCCESS');
	        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	            $find = ['${ORDER_NO}','${REMARK}'];
	            $replace = [$order->orderNo,$content];
	            WSTSendMsg($order->userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>1,'dataId'=>$order->orderId]);
	        } 
			//微信消息
			if((int)WSTConf('CONF.wxenabled')==1){
				$reasonData = WSTDatas('REFUND_TYPE',$refund->refundReson);
				$params = [];
				$params['ORDER_NO'] = $order->orderNo;
				$params['REASON'] = $reasonData['dataName'].(($refund->refundReson==10000)?" - ".$refund->refundOtherReson:"");           
				$params['MONEY'] = $refund->backMoney;
				WSTWxMessage(['CODE'=>'WX_ORDER_REFUND_SUCCESS','userId'=>$order->userId,'params'=>$params]);
			}
			//如果有钱剩下，那么就退回到供货商钱包
			$supplierMoneys = $order->realTotalMoney-$refund->backMoney;

			if(!$isOrderService && $supplierMoneys>0){
                //创建供货商资金流水
                $lm = [];
				$lm['targetType'] = 3;
				$lm['targetId'] = $order->supplierId;
				$lm['dataId'] = $order->orderId;
				$lm['dataSrc'] = 2;
				$lm['remark'] = '订单【'.$order->orderNo.'】退款，返回供货商金额¥'.$supplierMoneys."。";
				$lm['moneyType'] = 1;
				$lm['money'] = $supplierMoneys;
				$lm['payType'] = 0;
				$lm['createTime'] = date('Y-m-d H:i:s');
				model('LogMoneys')->add($lm);
				
		        
			}
			Db::commit();
			return WSTReturn("退款成功",1); 
        }catch (\Exception $e) {
			Db::rollback();
        }
		return WSTReturn("退款失败，请刷新后再重试"); 
	}
	/**
	 * 导出退款订单
	 */
	public function toExport(){
		$name='RefundOrder'.date('Ymd');
		$startDate = input('startDate');
		$endDate = input('endDate');
		$where = [];
		$where[] = ['o.dataFlag','=',1];
		$where[] = ['orderStatus','in',[-1,-3]];
		$orderNo = input('orderNo');
		$supplierName = input('supplierName');
		$deliverType = (int)input('deliverType',-1);
		$areaId1 = (int)input('areaId1');
		if($areaId1>0){
			$where[] = ['s.areaIdPath','like',"$areaId1%"];
			$areaId2 = (int)input("areaId1_".$areaId1);
			if($areaId2>0)$where[] = ['s.areaIdPath','like',$areaId1."_"."$areaId2%"];
			$areaId3 = (int)input("areaId1_".$areaId1."_".$areaId2);
			if($areaId3>0)$where[] = ['s.areaId','=',$areaId3];
		}
		$isRefund = (int)input('isRefund',-1);
		if($orderNo!='')$where[] = ['orderNo','like','%'.$orderNo.'%'];
		if($supplierName!='')$where[] = ['supplierName|supplierSn','like','%'.$supplierName.'%'];
		if($deliverType!=-1)$where[] = ['o.deliverType','=',$deliverType];
		if($isRefund!=-1)$where[] = ['o.isRefund','=',$isRefund];
		if($startDate!='' && $endDate!=''){
			$where[] = ['orf.createTime','between',[$startDate.' 00:00:00',$endDate.' 23:59:59']];
		}else if($startDate!=''){
			$where[] = ['orf.createTime','>=',$startDate.' 00:00:00'];
		}else if($endDate!=''){
			$where[] = ['orf.createTime','<=',$endDate.' 23:59:59'];
		}
		// 排序
		$sort = input('sort');
		$sort = str_replace('orderCodeTitle','orderCode',$sort);
		$order = [];
		if($sort!=''){
			$sortArr = explode('.',$sort);
			$order[$sortArr[0]] = $sortArr[1];
		}
		$page = Db::name('supplier_orders')->alias('o')->join('__SUPPLIERS__ s','o.supplierId=s.supplierId','left')
		->join('__USERS__ u','o.userId=u.userId','left')
		->join('__SUPPLIER_ORDER_REFUNDS__ orf ','o.orderId=orf.orderId and refundStatus in (1,2)')
		->where($where)
		->field('orf.id refundId,o.orderId,o.orderNo,s.supplierName,s.supplierId,s.supplierQQ,s.supplierWangWang,u.loginName,o.goodsMoney,o.totalMoney,o.realTotalMoney,o.orderunique
		,o.orderStatus,u.loginName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,orf.backMoney,orf.refundRemark,isRefund,orf.createTime,o.orderCode')
		->order($order)->order('orf.createTime', 'desc')->select();
		if(count($page)>0){
			foreach ($page as $v){
				$orderIds[] = $v['orderId'];
			}
			$goods = Db::name('order_goods')->where([['orderId','in',$orderIds]])->select();
			$goodsMap = [];
			foreach ($goods as $v){
				$v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
				$goodsMap[$v['orderId']][] = $v;
			}
			foreach ($page as $key => $v){
				$page[$key]['payType'] = WSTLangPayType($v['payType']);
				$page[$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
				$page[$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
				$page[$key]['orderCodeTitle'] = WSTOrderModule($v['orderCode']);
				$page[$key]['goods'] = $goodsMap[$v['orderId']];
			}
		}
		require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
		$objPHPExcel = new \PHPExcel();
		// 设置excel文档的属性
		$objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
		->setLastModifiedBy("WSTMart")//最后修改人
		->setTitle($name)//标题
		->setSubject($name)//题目
		->setDescription($name)//描述
		->setKeywords("退款订单");//种类
	
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
		$objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:N1');
		$objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$objRow->getFill()->getStartColor()->setRGB('666699');
		$objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
	
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '订单编号')->setCellValue('B1', '申请人')->setCellValue('C1', '供货商')->setCellValue('D1', '订单来源')->setCellValue('E1', '配送方式')
		->setCellValue('F1', '外部流水号')->setCellValue('G1', '订单商品')->setCellValue('H1', '商品价格')->setCellValue('I1', '数量')->setCellValue('J1', '实收金额')
		->setCellValue('K1', '申请退款金额')->setCellValue('L1', '申请时间')->setCellValue('M1', '退款状态')->setCellValue('N1', '退款备注');
		$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($styleArray);
	
		$i = 1;
		$totalRow = 0;
		for ($row = 0; $row < count($page); $row++){
			$goodsn = count($page[$row]['goods']);
			$i = $i+1;
			$i2 = $i3 = $i;
			$i = $i+(1*$goodsn)-1;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i2.':A'.$i)->mergeCells('B'.$i2.':B'.$i)->mergeCells('C'.$i2.':C'.$i)->mergeCells('D'.$i2.':D'.$i)->mergeCells('E'.$i2.':E'.$i)->mergeCells('F'.$i2.':F'.$i)
			->mergeCells('J'.$i2.':J'.$i)->mergeCells('K'.$i2.':K'.$i)->mergeCells('L'.$i2.':L'.$i)->mergeCells('M'.$i2.':M'.$i)->mergeCells('N'.$i2.':N'.$i);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, $page[$row]['orderNo'])->setCellValue('B'.$i2, $page[$row]['loginName'])->setCellValue('C'.$i2, $page[$row]['supplierName'])->setCellValue('D'.$i2, $page[$row]['orderCodeTitle'])
			->setCellValue('E'.$i2, $page[$row]['deliverType'])->setCellValue('F'.$i2, " ".$page[$row]['orderunique'])->setCellValue('J'.$i2, $page[$row]['realTotalMoney'])->setCellValue('K'.$i2, $page[$row]['backMoney'])
			->setCellValue('L'.$i2, $page[$row]['createTime'])->setCellValue('M'.$i2, ($page[$row]['isRefund']==1)?'已退款':'未退款')->setCellValue('N'.$i2, $page[$row]['refundRemark']);
			$objPHPExcel->getActiveSheet()->getStyle('O'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			for ($row2 = 0; $row2 < $goodsn; $row2++){
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i3, (($page[$row]['goods'][$row2]['goodsCode']=='gift')?'【赠品】':'').$page[$row]['goods'][$row2]['goodsName'].(($page[$row]['goods'][$row2]['goodsSpecNames']!='')?'【'.$page[$row]['goods'][$row2]['goodsSpecNames'].'】':''))->setCellValue('H'.$i3, $page[$row]['goods'][$row2]['goodsPrice'])->setCellValue('I'.$i3, $page[$row]['goods'][$row2]['goodsNum']);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$i3 = $i3 + 1;
			}
			$totalRow = $i3;
		}
		$totalRow = ($totalRow==0)?1:$totalRow-1;
	    $objPHPExcel->getActiveSheet()->getStyle('A1:N'.$totalRow)->applyFromArray(array(
				'borders' => array (
						'allborders' => array (
								'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
								'color' => array ('argb' => 'FF000000'),     //设置border颜色
						)
				)
		));
		$this->PHPExcelWriter($objPHPExcel,$name);
	}
}
