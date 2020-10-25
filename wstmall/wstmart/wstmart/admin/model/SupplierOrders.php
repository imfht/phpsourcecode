<?php
namespace wstmart\admin\model;
use think\Db;
use think\Loader;
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
 * 订单业务处理类
 */
class SupplierOrders extends Base{
	protected $pk = 'orderId';
	/**
	 * 获取用户订单列表
	 */
	public function pageQuery($orderStatus = 10000,$isAppraise = -1){
		$where = [];
		$where[] = ['o.dataFlag','=',1];
		if($orderStatus!=10000){
			$where[] = ['orderStatus','=',$orderStatus];
		}
		$startDate = input('startDate');
		$endDate = input('endDate');
		$orderNo = input('orderNo');
		$supplierName = input('supplierName');
		$orderCode = input('orderCode');
		$userId = (int)input('userId');
		$payType = (int)input('payType',-1);
		$deliverType = (int)input('deliverType',-1);
		$sort = input('sort');
		$payFrom = input('payFrom');
		$isInvoice = input('isInvoice/d',-1);
		$isRefund = input('isRefund/d',-1);
		if(in_array($isInvoice,[0,1]))$where[] = ['o.isInvoice','=',$isInvoice];
		if(in_array($isRefund,[0,1]))$where[] = ['o.isRefund','=',$isRefund];
		if($isAppraise!=-1)$where[] = ['isAppraise','=',$isAppraise];
		if($orderNo!='')$where[] = ['orderNo','like','%'.$orderNo.'%'];
		if($supplierName!='')$where[] = ['supplierName|supplierSn','like','%'.$supplierName.'%'];
		if($userId>0)$where[] = ['o.userId','=',$userId];
		if($orderCode!='')$where[] = ['orderCode','=',$orderCode];
        if($payFrom!='')$where[] = ['o.payFrom','=',$payFrom];
		if($startDate!='' && $endDate!=''){
			$where[] = ['o.createTime','between',[$startDate.' 00:00:00',$endDate.' 23:59:59']];
		}else if($startDate!=''){
			$where[] = ['o.createTime','>=',$startDate.' 00:00:00'];
		}else if($endDate!=''){
			$where[] = ['o.createTime','<=',$endDate.' 23:59:59'];
		}
		
		$areaId1 = (int)input('areaId1');

		if($areaId1>0){
			$where[] = ['s.areaIdPath','like',"$areaId1%"];
			$areaId2 = (int)input("areaId1_".$areaId1);
			if($areaId2>0)$where[] = ['s.areaIdPath','like',$areaId1."_"."$areaId2%"];
			$areaId3 = (int)input("areaId1_".$areaId1."_".$areaId2);
			if($areaId3>0)$where[] = ['s.areaId','=',$areaId3];
		}

		if($deliverType!=-1)$where[] = ['o.deliverType','=',$deliverType];
		if($payType!=-1)$where[] = ['o.payType','=',$payType];
		$order = 'o.createTime desc';
		if($sort){
			$sort =  str_replace('.',' ',$sort);
			$order = $sort;
		}
		$page = $this->alias('o')->join('__USERS__ u','o.userId=u.userId','left')->join('__SUPPLIERS__ s','o.supplierId=s.supplierId','left')->where($where)
		     ->field('o.orderId,o.orderNo,u.loginName,s.supplierName,s.supplierId,s.supplierQQ,s.supplierWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.userName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,o.createTime,o.orderCode')
			 ->order($order)
			 ->paginate(input('limit/d'))->toArray();
	    if(count($page['data'])>0){
	    	 foreach ($page['data'] as $key => $v){
	    	 	 $page['data'][$key]['userName'] = "【".$v['loginName']."】".$v['userName'];
	    	 	 $page['data'][$key]['payType'] = WSTLangPayType($v['payType']);
	    	 	 $page['data'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['data'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 	 $page['data'][$key]['orderCodeTitle'] = WSTOrderModule($v['orderCode']);
	    	 }
	    }
	    return $page;
	}
	
    /**
	 * 获取用户退款订单列表
	 */
	public function refundPageQuery(){
		$where = [];
		$where[] = ['o.dataFlag','=',1];
		$where[] = ['orderStatus','in',[-1,-4]];
		$where[] = ['o.payType','=',1];
		$orderNo = input('orderNo');
		$supplierName = input('supplierName');
		$deliverType = (int)input('deliverType',-1);
		$areaId1 = (int)input('areaId1');
		$areaId2 = (int)input('areaId2');
		$areaId3 = (int)input('areaId3');
		$isRefund = (int)input('isRefund',-1);
		if($orderNo!='')$where[] = ['orderNo','like','%'.$orderNo.'%'];
		if($supplierName!='')$where[] = ['supplierName|supplierSn','like','%'.$supplierName.'%'];
		if($areaId1>0)$where[] = ['s.areaId1','=',$areaId1];
		if($areaId2>0)$where[] = ['s.areaId2','=',$areaId2];
		if($areaId3>0)$where[] = ['s.areaId3','=',$areaId3];
		if($deliverType!=-1)$where[] = ['o.deliverType','=',$deliverType];
		if($isRefund!=-1)$where[] = ['o.isRefund','=',$isRefund];
		$page = $this->alias('o')->join('__SUPPLIERS__ s','o.supplierId=s.supplierId','left')
		     ->join('__SUPPLIER_ORDER_REFUNDS__ orf ','o.orderId=orf.orderId','left') 
		     ->where($where)
		     ->field('o.orderId,o.orderNo,s.supplierName,s.supplierId,s.supplierQQ,s.supplierWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.userName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,orf.refundRemark,isRefund,o.createTime')
			 ->order('o.createTime', 'desc')
			 ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['data'])>0){
	    	 foreach ($page['data'] as $key => $v){
	    	 	 $page['data'][$key]['payType'] = WSTLangPayType($v['payType']);
	    	 	 $page['data'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['data'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	/**
	 * 获取退款资料
	 */
	public function getInfoByRefund(){
		return $this->where([['orderId','=',(int)input('get.id')],['isRefund','=',0],['orderStatus','in',[-1,-4]]])
		         ->field('orderNo,orderId,goodsMoney,totalMoney,realTotalMoney,deliverMoney,payType,payFrom,tradeNo')
		         ->find();
	}
	/**
	 * 退款
	 */
	public function orderRefund(){
		$id = (int)input('post.id');
		$content = input('post.content');
		if($id==0)return WSTReturn("操作失败!");
		$order = $this->where([['orderId','=',(int)input('post.id')],['payType','=',1],['isRefund','=',0],['orderStatus','in',[-1,-4]]])
		         ->field('userId,orderNo,orderId,goodsMoney,totalMoney,realTotalMoney,deliverMoney,payType,payFrom,tradeNo')
		         ->find();
		if(empty($order))return WSTReturn("该订单不存在或已退款!");
		Db::startTrans();
        try{
			$order->isRefund = 1;
			$order->save();
			//修改用户账户金额
			Db::name('users')->where('userId',$order->userId)->setInc('userMoney',$order->realTotalMoney);
			//创建资金流水记录
			$lm = [];
			$lm['targetType'] = 0;
			$lm['targetId'] = $order->userId;
			$lm['dataId'] = $order->orderId;
			$lm['dataSrc'] = 1;
			$lm['remark'] = '订单【'.$order->orderNo.'】退款¥'.$order->realTotalMoney."。".(($content!='')?"【退款备注】：".$content:'');
			$lm['moneyType'] = 1;
			$lm['money'] = $order->realTotalMoney;
			$lm['payType'] = 0;
			$lm['createTime'] = date('Y-m-d H:i:s');
			model('LogMoneys')->save($lm);
			//创建退款记录
			$data = [];
			$data['orderId'] = $id;
			$data['refundRemark'] = $content;
			$data['refundTime'] = date('Y-m-d H:i:s');
			$rs = Db::name('supplier_order_refunds')->insert($data);
			if(false !== $rs){
				//发送一条用户信息
				WSTSendMsg($order['userId'],"您的退款订单【".$order['orderNo']."】已处理，请留意账户到账情况。".(($content!='')?"【退款备注：".$content."】":""),['from'=>1,'dataId'=>$id]);
				Db::commit();
				return WSTReturn("操作成功",1); 
			}
        }catch (\Exception $e) {

            Db::rollback();
        }
		return WSTReturn("操作失败，请刷新后再重试"); 
	}
	
	
	/**
	 * 获取订单详情
	 */
	public function getByView($orderId){
		$orders = $this->alias('o')
		               ->join('__SUPPLIER_ORDER_REFUNDS__ orf ','o.orderId=orf.orderId','left')
		               ->join('__SUPPLIERS__ s','o.supplierId=s.supplierId','left')
		               ->join('__USERS__ u','o.userId=u.userId','left')
		               ->where('o.dataFlag=1 and o.orderId='.$orderId)
		               ->field('o.*,u.loginName,s.supplierName,s.supplierQQ,s.supplierWangWang,orf.refundRemark,orf.refundTime,s.areaId supplierAreaId,s.supplierAddress')->find();
		if(empty($orders))return WSTReturn("无效的订单信息");
		// 获取店铺地址
		$orders['supplierAddr'] = model('common/areas')->getParentNames($orders['supplierAreaId']);
		$orders['supplierAddress'] = implode('',$orders['supplierAddr']).$orders['supplierAddress'];
		unset($orders['supplierAddr']);
		//获取订单信息
		$log = Db::name('supplier_log_orders')->where('orderId',$orderId)->order('logId asc')->select();
		$orders['log'] = [];
		$logs = [];
		$logFilter = [];
		foreach ($log as $key => $v) {
			if(in_array($orders['orderStatus'],[-2,0,1,2]) && in_array($v['orderStatus'],$logFilter))continue;
			$logs[] = $v; 
			$logFilter[] = $v['orderStatus'];
		}
		$orders['log'] = $logs;
		
		
		// 若为售后单
		$serviceId = (int)input('serviceId');
		if($serviceId>0){
			// 查询服务单下的商品信息
            $_goods = Db::name('supplier_service_goods')->alias('sg')
											   ->join('supplier_order_goods og','og.goodsId=sg.goodsId','inner')
											   ->where(['sg.serviceId'=>$serviceId,'og.orderId'=>$orderId])
											   ->field('sg.goodsNum,sg.serviceId,og.*')
											   ->select();
			// 计算售后单总金额及总积分、可获得的积分、使用积分数、积分抵扣金额
			$goodsMoney = 0;
			
			foreach($_goods as $k=>$v){
				// 总金额
				$goodsMoney += $v['goodsPrice']*$v['goodsNum'];
				
			}
			$orders['goods'] = $_goods;
			$orders['goodsMoney'] = $goodsMoney;
		}else{
			//获取订单商品
			$orders['goods'] = Db::name('supplier_order_goods')->where('orderId',$orderId)
			->order('id asc')->select();
		}
		return $orders;
	}
	
	/**
	 * 导出订单
	 */
	public function toExport(){
		$name='order';
		$where = [];
		$where[] = ['o.dataFlag','=',1];
		$orderStatus = (int)input('orderStatus',0);
		if($orderStatus==0){
			$name='PendingDelOrder';
		}else if($orderStatus==-2){
			$name='PendingPayorder';
		}else if($orderStatus==1){
			$name='DistributionOrder';
		}else if($orderStatus==10000){
			$name='order';
		}else if($orderStatus==-1){
			$name='CancelOrder';
		}else if($orderStatus==-3){
			$name='RejectionOrder';
		}else if($orderStatus==2){
			$name='ReceivedOrder';
		}
		$name = $name.date('Ymd');
		if($orderStatus!=10000){
			$where[] = ['o.orderStatus','=',$orderStatus];
		}
		$orderNo = input('orderNo');
		$supplierName = input('supplierName');
		$userId = (int)input('userId');
		$payType = (int)input('payType',-1);
		$deliverType = (int)input('deliverType',-1);
		$payFrom = input('payFrom');
		$isInvoice = input('isInvoice/d',-1);
		$isRefund = input('isRefund/d',-1);
		$startDate = input('startDate');
		$endDate = input('endDate');
		if($startDate!='')$where[] = ['o.createTime','>=',$startDate.' 00:00:00'];
		if($endDate!='')$where[] = ['o.createTime','<=',$endDate.' 23:59:59'];
		if(in_array($isInvoice,[0,1]))$where[] = ['o.isInvoice','=',$isInvoice];
		if(in_array($isRefund,[0,1]))$where[] = ['o.isRefund','=',$isRefund];
		if($payFrom!='')$where[] = ['payFrom','=',$payFrom];
		if($orderNo!='')$where[] = ['orderNo','like','%'.$orderNo.'%'];
		if($supplierName!='')$where[] = ['supplierName|supplierSn','like','%'.$supplierName.'%'];
		if($userId>0){
			$where[] = ['o.userId','=',$userId];
			$user = Db::name('users')->where('userId',$userId)->field('loginName')->find();
			$name = $user['loginName'].'Order';
		}
		$areaId1 = (int)input('areaId1');
		if($areaId1>0){
			$where[] = ['s.areaIdPath','like',"$areaId1%"];
			$areaId2 = (int)input("areaId1_".$areaId1);
			if($areaId2>0)$where[] = ['s.areaIdPath','like',$areaId1."_"."$areaId2%"];
			$areaId3 = (int)input("areaId1_".$areaId1."_".$areaId2);
			if($areaId3>0)$where[] = ['s.areaId','=',$areaId3];
		}
		
		if($deliverType!=-1)$where[] = ['o.deliverType','=',$deliverType];
		if($payType!=-1)$where[] = ['o.payType','=',$payType];
		$page = $this->alias('o')->where($where)
		->join('__USERS__ u','o.userId=u.userId','left')
		->join('__SUPPLIERS__ s','o.supplierId=s.supplierId','left')
		->join('__SUPPLIER_LOG_ORDERS__ lo','lo.orderId=o.orderId and lo.orderStatus in (-1,-3) ','left')
		->field('o.orderId,o.orderNo,u.loginName,s.supplierName,s.supplierId,s.supplierQQ,s.supplierWangWang,u.loginName,o.goodsMoney,o.totalMoney,o.realTotalMoney,o.deliverMoney,lo.logContent,o.orderunique,o.payTime,o.payFrom,o.tradeNo
		,o.invoiceJson,o.isMakeInvoice,o.isInvoice,o.isRefund,o.orderStatus,o.userName,o.userAddress,o.userPhone,o.orderRemarks,o.invoiceClient,o.receiveTime,o.deliveryTime,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,o.createTime,s.areaId supplierAreaId,s.supplierAddress')
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
				$page[$key]['goods'] = $goodsMap[$v['orderId']];
                $page[$key]['isMakeInvoice'] = ($v['isMakeInvoice']==1)?'已开':'未开';
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
		->setKeywords("订单");//种类
	
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(32);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
		$objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:Z1');
		$objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$objRow->getFill()->getStartColor()->setRGB('666699');
		$objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '订单编号')->setCellValue('B1', '订单状态')->setCellValue('C1', '商家')->setCellValue('D1', '会员')->setCellValue('E1', '收货人')
		->setCellValue('F1', '收货地址')->setCellValue('G1', '联系方式')->setCellValue('H1', '支付方式')->setCellValue('I1', '支付来源')->setCellValue('J1', '外部流水号')
		->setCellValue('K1', '配送方式')->setCellValue('L1', '发票状态')->setCellValue('Z1', '买家留言')->setCellValue('M1', '发票信息')->setCellValue('N1', '订单商品')->setCellValue('O1', '商品价格')->setCellValue('P1', '数量')
		->setCellValue('Q1', '订单总金额')->setCellValue('R1', '运费')->setCellValue('S1', '实付金额')->setCellValue('T1', '下单时间')->setCellValue('U1', '付款时间')->setCellValue('V1', '发货时间')
		->setCellValue('W1', '收货时间')->setCellValue('X1', '取消/拒收原因')->setCellValue('Y1', '是否退款');
	    $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($styleArray);
		$i = 1;
		$totalRow = 0;
		for ($row = 0; $row < count($page); $row++){
			$goodsn = count($page[$row]['goods']);
			$i = $i+1;
			$i2 = $i3 = $i;
			$i = $i+(1*$goodsn)-1;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i2.':A'.$i)->mergeCells('B'.$i2.':B'.$i)->mergeCells('C'.$i2.':C'.$i)->mergeCells('D'.$i2.':D'.$i)->mergeCells('E'.$i2.':E'.$i)->mergeCells('F'.$i2.':F'.$i)
			->mergeCells('G'.$i2.':G'.$i)->mergeCells('H'.$i2.':H'.$i)->mergeCells('I'.$i2.':I'.$i)->mergeCells('J'.$i2.':J'.$i)->mergeCells('K'.$i2.':K'.$i)->mergeCells('L'.$i2.':L'.$i)->mergeCells('Z'.$i2.':Z'.$i)->mergeCells('M'.$i2.':M'.$i)
			->mergeCells('Q'.$i2.':Q'.$i)->mergeCells('R'.$i2.':R'.$i)->mergeCells('S'.$i2.':S'.$i)->mergeCells('T'.$i2.':T'.$i)->mergeCells('U'.$i2.':U'.$i)->mergeCells('V'.$i2.':V'.$i)->mergeCells('W'.$i2.':W'.$i)
			->mergeCells('X'.$i2.':X'.$i)->mergeCells('Y'.$i2.':Y'.$i);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, $page[$row]['orderNo'])->setCellValue('B'.$i2, $page[$row]['status'])->setCellValue('C'.$i2, $page[$row]['supplierName'])->setCellValue('D'.$i2, $page[$row]['loginName'])
			->setCellValue('E'.$i2, $page[$row]['userName'])->setCellValue('F'.$i2, $page[$row]['userAddress'])->setCellValue('G'.$i2, $page[$row]['userPhone'])->setCellValue('H'.$i2, $page[$row]['payTypeName'])
			->setCellValue('I'.$i2, ($page[$row]['payFrom'])?WSTLangPayFrom($page[$row]['payFrom']):'')->setCellValue('J'.$i2, " ".$page[$row]['tradeNo'])->setCellValue('K'.$i2, $page[$row]['deliverType'])->setCellValue('L'.$i2, $page[$row]['isMakeInvoice'])->setCellValue('Z'.$i2, $page[$row]['orderRemarks'])
			->setCellValue('M'.$i2, $page[$row]['invoiceArr'])->setCellValue('Q'.$i2, $page[$row]['totalMoney'])->setCellValue('R'.$i2, $page[$row]['deliverMoney'])->setCellValue('S'.$i2, $page[$row]['realTotalMoney'])
			->setCellValue('T'.$i2, $page[$row]['createTime'])->setCellValue('U'.$i2, $page[$row]['payTime'])->setCellValue('V'.$i2, $page[$row]['deliveryTime']) 
			->setCellValue('W'.$i2, $page[$row]['receiveTime'])->setCellValue('X'.$i2, $page[$row]['logContent'])->setCellValue('Y'.$i2, ($page[$row]['isRefund']==1)?'是':'');
			$objPHPExcel->getActiveSheet()->getStyle('F'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('X'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			for ($row2 = 0; $row2 < $goodsn; $row2++){
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i3, (($page[$row]['goods'][$row2]['goodsCode']=='gift')?'【赠品】':'').$page[$row]['goods'][$row2]['goodsName'].(($page[$row]['goods'][$row2]['goodsSpecNames']!='')?"【".$page[$row]['goods'][$row2]['goodsSpecNames']."】":''))->setCellValue('O'.$i3, $page[$row]['goods'][$row2]['goodsPrice'])->setCellValue('P'.$i3, $page[$row]['goods'][$row2]['goodsNum']);
				$objPHPExcel->getActiveSheet()->getStyle('N'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$i3 = $i3 + 1;
			}
			$totalRow = $i3;
		}
		$totalRow = ($totalRow==0)?1:$totalRow-1;
	    $objPHPExcel->getActiveSheet()->getStyle('A1:Z'.$totalRow)->applyFromArray(array(
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
	 * 修改支付状态
	 */
	public function changePayStatus($order){
		if($order->orderStatus!=-2)return WSTReturn('订单状态不在待支付状态中');
		$data = [];
		$data['isBatch'] = 0;
		$data['trade_no'] = input('trade_no');
		$data['payFrom'] = input('payFrom');
		$data['out_trade_no'] = $order->orderNo;
		$data['total_fee'] = $order['needPay'];
		$data['userId'] = $order->userId;
		$rs = model('shop/supplierOrders')->complatePay($data);
		if($rs['status']==1){
			return WSTReturn('操作成功',1);
		}else{
            return WSTReturn('操作失败');   
		}
	}
	/**
	 * 取消订单
	 */
	public function cancelOrder($order){
        if(!in_array($order->orderStatus,[-2,0]))return WSTReturn('订单状态不在可取消的范围内');
        $orderId = $order->orderId;
        Db::startTrans();
		try{
			//把实付金额设置为0
			if($order->payType==0 || ($order->payType==1 && $order->isPay==0)){
				$order->realTotalMoney = 0;
				$order->isClosed = 1;
			}
			$order->orderStatus =-1;
			$order->cancelReason =5;
			$result = $order->save();
            //正常订单商品库存处理
            $goods = Db::name('supplier_order_goods')->alias('og')->join('__SUPPLIER_GOODS__ g','og.goodsId=g.goodsId','inner')
						   ->where('orderId',$orderId)->field('og.*,g.isSpec')->select();
            //返还商品库存
			foreach ($goods as $key => $v){
					//处理虚拟产品
					if($v['goodsType']==1){
		                $extraJson = json_decode($v['extraJson'],true);
		                foreach ($extraJson as  $ecard) {
		                    Db::name('supplier_goods_virtuals')->where('id',$ecard['cardId'])->update(['orderId'=>0,'orderNo'=>'','isUse'=>0]);
		                }
		                $counts = Db::name('supplier_goods_virtuals')->where(['dataFlag'=>1,'goodsId'=>$v['goodsId'],'isUse'=>0])->count();
		                Db::name('supplier_goods')->where('goodsId',$v['goodsId'])->setField('goodsStock',$counts);
					}else{
						if($order['orderCode']=='order'){
							//修改库存
							if($v['isSpec']>0){
								Db::name('supplier_goods_specs')->where('id',$v['goodsSpecId'])->setInc('specStock',$v['goodsNum']);
							}
							Db::name('supplier_goods')->where('goodsId',$v['goodsId'])->setInc('goodsStock',$v['goodsNum']);
						}
					}
	        }
		    //新增订单日志
			$logOrder = [];
			$logOrder['orderId'] = $orderId;
			$logOrder['orderStatus'] = -1;
			$logOrder['logContent'] = "管理员取消订单";
			$logOrder['logUserId'] = $order->userId;
			$logOrder['logType'] = 0;
			$logOrder['logTime'] = date('Y-m-d H:i:s');
			Db::name('supplier_log_orders')->insert($logOrder);
			//发送一条商家信息
			$tpl = WSTMsgTemplates('ORDER_CANCEL');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1'){
		       $find = ['${ORDER_NO}','${REASON}'];
		        $replace = [$order->orderNo,'管理员取消订单'];
		                   
		        $msg = array();
				$msg["supplierId"] = $order->supplierId;
				$msg["tplCode"] = $tpl["tplCode"];
				$msg["msgType"] = 1;
				$msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
				$msg["msgJson"] = ['from'=>1,'dataId'=>$orderId];
				model("common/SupplierMessageQueues")->add($msg);
		    }
		    
            $refundId = 0;
	        //有使用积分和钱的取消订单自动申请退款
	        if($order->isPay==1){
	            $data = [];
			    $data['orderId'] = $orderId;
			    $data['refundTo'] = 0;
			    $data['refundReson'] = 10000;
			    $data['refundOtherReson'] = '管理员取消订单';
			    $data['backMoney'] = $order->realTotalMoney;
			    $data['createTime'] = date('Y-m-d H:i:s');
			    $data['refundStatus'] = 1;
			    $refundId = Db::name('supplier_order_refunds')->insertGetId($data);
	        }
		    Db::commit();
			return WSTReturn('订单取消成功',1,['refundId'=>$refundId]);
        }catch (\Exception $e) {
		    Db::rollback();
	        return WSTReturn('操作失败',-1);
	    }
	    return WSTReturn('操作失败',-1);
	}
	/**
	 * 确认订单收货
	 */
	public function receiveOrder($order){
		if(!in_array($order->orderStatus,[1,-3]))return WSTReturn('订单状态不在可确认收货的范围内');
		$orderId = $order->orderId;
		$userId = $order->userId;
		$order = $this->alias('o')->join('__SUPPLIERS__ s','o.supplierId=s.supplierId','left')
		              ->where(['o.orderId'=>$orderId])
		              ->field('o.orderId,o.orderNo,o.payType,s.userId,s.supplierId,o.realTotalMoney,commissionFee')->find();
		if(!empty($order)){
			Db::startTrans();
		    try{
		    	//结束订单状态
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
					//发送一条商家信息
					$tpl = WSTMsgTemplates('ORDER_RECEIVE');
	                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                    $find = ['${ORDER_NO}'];
	                    $replace = [$order['orderNo']];
	                    
	                	$msg = array();
			            $msg["supplierId"] = $order["supplierId"];
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
	 * 修改订单状态
	 */
	public function changeOrderStatus(){
		$orderStatus = (int)input('orderStatus');
		$orderId = (int)input('orderId');
		if(!in_array($orderStatus,[0,-1,2]))return WSTReturn('无效的订单状态');
		$order = $this->where(['orderId'=>$orderId,'dataFlag'=>1])->find();
		//设置订单已支付
		switch($orderStatus){
			case 0: return $this->changePayStatus($order);
			case -1:return $this->cancelOrder($order);
			case 2: return $this->receiveOrder($order);
		}
		return WSTReturn('操作失败');
	}
}
