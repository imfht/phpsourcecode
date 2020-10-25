<?php
namespace wstmart\store\model;
use think\Db;
use Env;
use think\Loader;
use wstmart\common\model\LogSms;
use wstmart\common\model\OrderRefunds as M;
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
class Orders extends Base{
	protected $pk = 'orderId';
	
	
	/**
	 * 获取商家订单
	 */
	public function storeOrdersByPage($orderStatus){
		$orderNo = input('post.orderNo');
		$shopName = input('post.shopName');
		$payType = (int)input('post.payType');
		// 未退款订单
		$refund = (int)input('post.refund');

		$shopId = (int)session('WST_STORE.shopId');
		$storeId = (int)session('WST_STORE.storeId');

		$where = ['shopId'=>$shopId,'storeId'=>$storeId,'storeType'=>1,'dataFlag'=>1];
        $condition = [];
		if(is_array($orderStatus)){
			$condition[] = ['orderStatus','in',$orderStatus];
		}else{
			$where['orderStatus'] = $orderStatus;
		}
		if($orderNo!=''){
			$condition[] = ['orderNo','like',"%$orderNo%"];
		}
		if($shopName!=''){
			$condition[] = ['shopName','like',"%$shopName%"];
		}
		if($payType > -1){
			$where['payType'] =  $payType;
		}
		
		if($refund > 0){
			$condition[] =  ['orf.id','gt',0];
			$condition[] =  ['o.isRefund','=',0];
		}

		$page = $this->alias('o')->where($where)->where($condition)
		      ->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and refundStatus=0','left')
		      ->field('o.orderRemarks,o.noticeDeliver,o.orderId,orderNo,goodsMoney,totalMoney,
		      	realTotalMoney,orderStatus,deliverType,deliverMoney,isAppraise,isRefund,payType,
		      	payFrom,userAddress,orderStatus,isPay,isAppraise,userName,orderSrc,o.createTime,orf.id refundId,o.orderCode')
			  ->order('o.createTime', 'desc')
			  ->paginate()->toArray();
	    if(count($page['data'])>0){
	    	 $orderIds = [];
	    	 foreach ($page['data'] as $v){
	    	 	 $orderIds[] = $v['orderId'];
	    	 }
	    	 $goods = Db::name('order_goods')->where([['orderId','in',$orderIds]])->select();
	    	 $goodsMap = [];
	    	 foreach ($goods as $v){
	    	 	$v['goodsName'] = WSTStripTags($v['goodsName']);
	    	 	$v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
	    	 	$goodsMap[$v['orderId']][] = $v;
	    	 }
	    	 foreach ($page['data'] as $key => $v){
	    	 	 $page['data'][$key]['orderCodeTitle'] = WSTOrderModule($v['orderCode']);
	    	 	 $page['data'][$key]['list'] = $goodsMap[$v['orderId']];
	    	 	 $page['data'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
	    	 	 $page['data'][$key]['deliverTypeName'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['data'][$key]['deliverType'] = $v['deliverType'];
	    	 	 $page['data'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	/**
	 * 商家发货
	 */
	public function deliver(){
		$orderId = (int)input('post.id');
		$expressId = (int)input('post.expressId');
		$expressNo = ($expressId>0)?input('post.expressNo'):'';
        $shopId = (int)session('WST_STORE.shopId');
        $storeId = (int)session('WST_STORE.storeId');
        $userId = (int)session('WST_STORE.userId');
        $order = $this->where(['shopId'=>$shopId,'storeId'=>$storeId,'storeType'=>1,'orderId'=>$orderId,'orderStatus'=>0])
				        ->field('orderId,orderNo,userId,deliverType')
				        ->find();
        if(empty($order))return WSTReturn('操作失败，请检查订单状态是否已改变');
        
        $finishDeliver = false;
        if($order['deliverType'] == 1){//客户自提
            $finishDeliver = true;
        }else{
        	return WSTReturn('该订单不是自提订单');
        }
		Db::startTrans();
		try{
            
            $orderStatus = 0;
            if($finishDeliver){
            	$orderStatus = 1;
                $orderData = ['orderStatus'=>1,'deliveryTime'=>date('Y-m-d H:i:s')];
                $result = $this->where([['orderId','=',$order['orderId']],['shopId','=',$shopId]])->update($orderData);
            }
		    //新增订单日志
			$logOrder = [];
			$logOrder['orderId'] = $orderId;
			$logOrder['orderStatus'] = $orderStatus;
			$logOrder['logContent'] = "门店已发货";
			$logOrder['logUserId'] = $userId;
			$logOrder['logType'] = 0;
			$logOrder['logTime'] = date('Y-m-d H:i:s');
			Db::name('log_orders')->insert($logOrder);
			//发送一条用户信息
			$tpl = WSTMsgTemplates('ORDER_DELIVERY');
	        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	            $find = ['${ORDER_NO}','${EXPRESS_NO}'];
	            $replace = [$order['orderNo'],($expressNo=='')?'无':$expressNo];
	            WSTSendMsg($order['userId'],str_replace($find,$replace,$tpl['tplContent']),['from'=>1,'dataId'=>$orderId]);
	        }
	        //微信消息
	        if(WSTDatas('ADS_TYPE',3)!='' || WSTDatas('ADS_TYPE',4)!=''){
		        $params = [];
		        if($expressId>0){
		            $express = model('express')->get($expressId);
		            $params['EXPRESS'] = $express->expressName;          
		            $params['EXPRESS_NO'] = $expressNo;       
		        }else{
		            $params['EXPRESS'] = '无';
		            $params['EXPRESS_NO'] = '无';
		        }
		        $params['ORDER_NO'] = $order['orderNo'];  
		        if(WSTConf('CONF.wxenabled')==1){
			        WSTWxMessage(['CODE'=>'WX_ORDER_DELIVERY','userId'=>$order['userId'],'URL'=>Url('wechat/orders/index',['type'=>'waitReceive'],true,true),'params'=>$params]);
			    }
			}
			Db::commit();
			hook("afterShopDeliver",['orderId'=>$orderId,'shopId'=>$shopId,'uerSystem'=>[0,1],'printCatId'=>2]);
			return WSTReturn('操作成功',1);
		}catch (\Exception $e) {
	        Db::rollback();
	       return WSTReturn('操作失败',-1);
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	
	
	/**
	 * 获取订单详情
	 */
	public function getByView($orderId){
		$shopId = (int)session('WST_STORE.shopId');
		$storeId = (int)session('WST_STORE.storeId');
		$where = [];
		$where[] = ['o.shopId','=',$shopId];
		$where[] = ['o.storeId','=',$storeId];
		$where[] = ['o.storeType','=',1];
		$where[] = ['o.dataFlag','=',1];
		$where[] = ['o.orderId','=',$orderId];
		
		$orders = Db::name('orders')->alias('o')
		               ->join('__SHOPS__ s','o.shopId=s.shopId','left')
		               ->join('__ORDER_COMPLAINS__ oc','oc.orderId=o.orderId','left')
		               ->join('__ORDER_REFUNDS__ orf ','o.orderId=orf.orderId','left')
		               ->where($where)
		               ->field('o.*,s.areaId shopAreaId,s.shopAddress,s.shopTel,s.shopName,s.shopQQ,s.shopWangWang,orf.id refundId,orf.refundRemark,orf.refundStatus,orf.refundTime,orf.backMoney,orf.backMoney,oc.complainId')->find();
		if(empty($orders))return WSTReturn("无效的订单信息");
		// 获取店铺地址
		$orders['shopAddr'] = model('common/areas')->getParentNames($orders['shopAreaId']);
		$orders['shopAddress'] = implode('',$orders['shopAddr']).$orders['shopAddress'];
		unset($orders['shopAddr']);
		//获取订单信息
		$log = Db::name('log_orders')->where('orderId',$orderId)->order('logId asc')->select();
		$orders['log'] = [];
		$logFilter = [];
		foreach ($log as $key => $v) {
			if(in_array($orders['orderStatus'],[-2,0,1,2]) && in_array($v['orderStatus'],$logFilter))continue;
			$orders['log'][] = $v; 
			$logFilter[] = $v['orderStatus'];
		}
		//获取订单商品
		$orders['goods'] = Db::name('order_goods')->alias('og')->join('__GOODS__ g','g.goodsId=og.goodsId','left')->where('orderId',$orderId)->field('og.*,g.goodsSn')->order('id asc')->select();
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
        $orderExpressNos = Db::name('order_express')->where([['orderId','=',$orderId],['isExpress','=',1]])->column("expressNo");
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
	 	if($orders['payType']==1 && $orders['isRefund']==0 && $orders['refundId']=='' && ($orders['isPay'] ==1 || $orders['useScore']>0)){
              $orders['allowRefund'] = 1;
	 	}
	 	//货到付款中使用了积分支付的也可以申请退款
	 	if($orders['payType']==0 && $orders['useScore']>0 && $orders['refundId']=='' && $orders['isRefund']==0){
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
				$ogNum = Db::name('order_goods')
						 ->where(['orderId'=>$orderId])
						 ->value('sum(goodsNum) ogNum');
				$osNum = Db::name('order_services')->alias('os')
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
		$userId = (int)session('WST_STORE.userId');

		$goodsInfo = Db::name('order_goods')
					->field('id,orderId,goodsName,goodsId,goodsSpecNames,goodsImg,goodsSpecId,goodsCode')
					->where(['orderId'=>$orderId])
					->select();
		//根据商品id 与 订单id 取评价
		$alreadys = 0;// 已评价商品数
		$count = count($goodsInfo);//订单下总商品数
		if($count>0){
			foreach($goodsInfo as $k=>$v){
				$goodsInfo[$k]['goodsSpecNames'] = str_replace('@@_@@', ';', $v['goodsSpecNames']);

				// app端处理
				if($uId>0 && isset($v['goodsName'])){
					$goodsInfo[$k]['goodsName'] = htmlspecialchars_decode($v['goodsName']);
				}

				$appraise = Db::name('goods_appraises')
							->field('goodsScore,serviceScore,timeScore,content,images,createTime')
							->where(['goodsId'=>$v['goodsId'],
							         'goodsSpecId'=>$v['goodsSpecId'],
									 'orderId'=>$orderId,
									 'dataFlag'=>1,
									 'userId'=>$userId,
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
		$shopId = (int)session('WST_STORE.shopId');
		$storeId = (int)session('WST_STORE.storeId');
		$where = [];
		$where[] = ['o.shopId','=',$shopId];
		$where[] = ['o.storeId','=',$storeId];
		$where[] = ['o.storeType','=',1];
		$orderNo = input('orderNo');
		$shopName = input('shopName');
		
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
		if($shopName!=''){
			$where[] = ['shopName','like',"%$shopName%"];
		}
		if($payType > -1){
			$where[] =  ['payType','=',$payType];
		}
		
		$page = $this->alias('o')->where($where)
				->join('__SHOPS__ s','o.shopId=s.shopId')
				->join('__STORES__ st','o.storeId=st.storeId and o.storeType=1')
				->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and refundStatus=0','left')
				->join('__LOG_ORDERS__ lo','lo.orderId=o.orderId and lo.orderStatus in (-1,-3) ','left')
				->field('o.orderId,orderNo,goodsMoney,totalMoney,realTotalMoney,o.orderStatus,deliverType,
					deliverMoney,isAppraise,o.deliverMoney,lo.logContent,o.payTime,o.payFrom,
					o.invoiceJson,o.isMakeInvoice,o.isInvoice,o.isRefund,payType,o.userName,o.userAddress,
					o.userPhone,o.orderRemarks,o.invoiceClient,o.receiveTime,o.deliveryTime,orderSrc,o.createTime,
					orf.id refundId,s.areaId shopAreaId,s.shopAddress,st.storeName')
				->order('o.createTime', 'desc')
				->select();
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
				$page[$key]['invoiceArr'] = '';
				if($v['isInvoice']==1){
					$invoiceArr = json_decode($v['invoiceJson'],true);
					$page[$key]['invoiceArr'] = " ".$invoiceArr['invoiceHead'];
					if(isset($invoiceArr['invoiceCode'])){
						$page[$key]['invoiceArr'] = " ".$invoiceArr['invoiceHead'].'|'.$invoiceArr['invoiceCode'];
					}
				}
				$page[$key]['shopAddr'] = model('common/areas')->getParentNames($v['shopAreaId']);
		        $page[$key]['shopAddress'] = implode('',$v['shopAddr']).$v['shopAddress'];
		        if($page[$key]['deliverType']==1)$page[$key]['userAddress'] = $page[$key]['shopAddress'];
				$page[$key]['payTypeName'] = WSTLangPayType($v['payType']);
				$page[$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
				$page[$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
				$page[$key]['goods'] = $goodsMap[$v['orderId']];
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(50);
		$objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:X1');
		$objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$objRow->getFill()->getStartColor()->setRGB('666699');
		$objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', '订单编号')
					->setCellValue('B1', '订单状态')
					->setCellValue('C1', '收货人')
					->setCellValue('D1', '收货地址')
					->setCellValue('E1', '联系方式')
					->setCellValue('F1', '支付方式')
					->setCellValue('G1', '支付来源')
					->setCellValue('H1', '配送方式')
					->setCellValue('I1', '发票状态')
					->setCellValue('J1', '发票信息')
					->setCellValue('K1', '订单商品')
					->setCellValue('L1', '商品价格')
					->setCellValue('M1', '数量')
					->setCellValue('N1', '订单总金额')
					->setCellValue('O1', '运费')
					->setCellValue('P1', '实付金额')
					->setCellValue('Q1', '下单时间')
					->setCellValue('R1', '付款时间')
					->setCellValue('S1', '发货时间')
					->setCellValue('T1', '收货时间')
					->setCellValue('U1', '取消/拒收原因')
					->setCellValue('V1', '是否退款')
					->setCellValue('W1', '买家留言')
					->setCellValue('X1', '自提门店');
		$objPHPExcel->getActiveSheet()->getStyle('A1:X1')->applyFromArray($styleArray);
		$i = 1;
		$totalRow = 0;
		for ($row = 0; $row < count($page); $row++){
			$goodsn = count($page[$row]['goods']);
			$i = $i+1;
			$i2 = $i3 = $i;
			$i = $i+(1*$goodsn)-1;
			$objPHPExcel->getActiveSheet()
						->mergeCells('A'.$i2.':A'.$i)
						->mergeCells('B'.$i2.':B'.$i)
						->mergeCells('C'.$i2.':C'.$i)
						->mergeCells('D'.$i2.':D'.$i)
						->mergeCells('E'.$i2.':E'.$i)
						->mergeCells('F'.$i2.':F'.$i)
						->mergeCells('G'.$i2.':G'.$i)
						->mergeCells('H'.$i2.':H'.$i)
						->mergeCells('I'.$i2.':I'.$i)
						->mergeCells('J'.$i2.':J'.$i)
						->mergeCells('N'.$i2.':N'.$i)
						->mergeCells('O'.$i2.':O'.$i)
						->mergeCells('P'.$i2.':P'.$i)
						->mergeCells('Q'.$i2.':Q'.$i)
						->mergeCells('R'.$i2.':R'.$i)
						->mergeCells('S'.$i2.':S'.$i)
						->mergeCells('T'.$i2.':T'.$i)
						->mergeCells('U'.$i2.':U'.$i)
						->mergeCells('V'.$i2.':V'.$i)
						->mergeCells('W'.$i2.':W'.$i)
						->mergeCells('X'.$i2.':X'.$i);
			$objPHPExcel->getActiveSheet()
			->setCellValue('A'.$i2, $page[$row]['orderNo'])
			->setCellValue('B'.$i2, $page[$row]['status'])
			->setCellValue('C'.$i2, $page[$row]['userName'])
			->setCellValue('D'.$i2, $page[$row]['userAddress'])
			->setCellValue('E'.$i2, $page[$row]['userPhone'])
			->setCellValue('F'.$i2, $page[$row]['payTypeName'])
			->setCellValue('G'.$i2, ($page[$row]['payFrom'])?WSTLangPayFrom($page[$row]['payFrom']):'')
			->setCellValue('H'.$i2, $page[$row]['deliverType'])
			->setCellValue('I'.$i2, $page[$row]['isMakeInvoice'])
			->setCellValue('W'.$i2, $page[$row]['orderRemarks'])
			->setCellValue('J'.$i2, $page[$row]['invoiceArr'])
			->setCellValue('N'.$i2, $page[$row]['totalMoney'])
			->setCellValue('O'.$i2, $page[$row]['deliverMoney'])
			->setCellValue('P'.$i2, $page[$row]['realTotalMoney'])
			->setCellValue('Q'.$i2, $page[$row]['createTime'])
			->setCellValue('R'.$i2, $page[$row]['payTime'])
			->setCellValue('S'.$i2, $page[$row]['deliveryTime'])
			->setCellValue('T'.$i2, $page[$row]['receiveTime'])
			->setCellValue('U'.$i2, $page[$row]['logContent'])
			->setCellValue('V'.$i2, ($page[$row]['isRefund']==1)?'是':'')
			->setCellValue('X'.$i2, $page[$row]['storeName']);

			$objPHPExcel->getActiveSheet()->getStyle('D'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('U'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			for ($row2 = 0; $row2 < $goodsn; $row2++){
				$objPHPExcel->getActiveSheet()
				->setCellValue('K'.$i3, (($page[$row]['goods'][$row2]['goodsCode']=='gift')?'【赠品】':'').$page[$row]['goods'][$row2]['goodsName'].(($page[$row]['goods'][$row2]['goodsSpecNames']!='')?'【'.$page[$row]['goods'][$row2]['goodsSpecNames'].'】':''))
				->setCellValue('L'.$i3, $page[$row]['goods'][$row2]['goodsPrice'])
				->setCellValue('M'.$i3, $page[$row]['goods'][$row2]['goodsNum']);
				$objPHPExcel->getActiveSheet()->getStyle('K'.$i3)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$i3 = $i3 + 1;
			}
			$totalRow = $i3;
		}
	    $totalRow = ($totalRow==0)?1:$totalRow-1;
	    $objPHPExcel->getActiveSheet()->getStyle('A1:X'.$totalRow)->applyFromArray(array(
				'borders' => array (
						'allborders' => array (
								'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
								'color' => array ('argb' => 'FF000000'),     //设置border颜色
						)
				)
		));
		$this->PHPExcelWriter($objPHPExcel,$name);
	}
	
	

    /**
     * 获取单条订单的商品信息
     */
    public function waitDeliverById(){
        $orderId = (int)input('id');
        $goods = Db::name('order_goods')->where('orderId','=',$orderId)->select();
        $order = Db::name('orders')->field('deliverType,userAddress,userName,userPhone')->where('orderId','=',$orderId)->find();
        $orderExpressGoodsIds = Db::name('order_express')->field('orderGoodsId')->where(['orderId'=>$orderId])->select();
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

}
