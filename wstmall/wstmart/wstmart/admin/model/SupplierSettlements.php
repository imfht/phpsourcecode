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
 * 结算业务处理
 */
class SupplierSettlements extends Base{
    protected $pk = 'settlementId';
    /**
	 * 获取结算列表
	 */
	public function pageQuery(){
        $where = [];
        $startDate = input('startDate');
		$endDate = input('endDate');
		$supplierName = input('supplierName');
        $settlementNo = input('settlementNo');
		$settlementStatus = (int)input('settlementStatus',-1);
		$sort = input('sort');
        $where = [];
        if($startDate!='')$where[] = ['st.createTime','>=',$startDate.' 00:00:00'];
        if($endDate!='')$where[] = ['st.createTime','<=',$endDate.' 23:59:59'];
		if($settlementNo!='')$where[] = ['settlementNo','like','%'.$settlementNo.'%'];
        if($supplierName!='')$where[] = ['supplierName|supplierSn','like','%'.$supplierName.'%']; 
        if($settlementStatus>=0)$where[] = ['settlementStatus','=',$settlementStatus];
        $order = 'st.settlementId desc';
        if($sort){
        	$sortArr = explode('.',$sort);
        	$order = $sortArr[0].' '.$sortArr[1];
        	if($sortArr[0]=='settlementNo'){
        		$order = $sortArr[0].'+0 '.$sortArr[1];
        	}
        }
		return Db::name('supplier_settlements')->alias('st')->join('suppliers s','s.supplierId=st.supplierId','left')->where($where)->field('s.supplierName,settlementNo,settlementId,settlementMoney,commissionFee,backMoney,settlementStatus,settlementTime,st.createTime')->order($order)
			->paginate(input('limit/d'))->toArray();
	}

	/**
	 * 获取结算订单详情
	 */
	public function getById(){
        $settlementId = (int)input('id');
        $object =  Db::name('supplier_settlements')->alias('st')->where('settlementId',$settlementId)->join('suppliers s','s.supplierId=st.supplierId','left')->field('s.supplierName,st.*')->find();
        if(!empty($object)){
        	$object['list'] = Db::name('supplier_orders')->where(['settlementId'=>$settlementId])
        	          ->field('orderId,orderNo,payType,goodsMoney,deliverMoney,realTotalMoney,totalMoney,commissionFee,createTime')
        	          ->order('payType desc,orderId desc')->select();
        }
        return $object;
	}
	

	/**
	 * 获取订单商品
	 */
	public function pageGoodsQuery(){
        $id = (int)input('id');
        return Db::name('supplier_orders o')->join('supplier_order_goods og','o.orderId=og.orderId')->where('o.settlementId',$id)
        ->field('orderNo,og.goodsPrice,og.goodsName,og.goodsSpecNames,og.goodsNum,og.commissionRate')->order('o.payType desc,o.orderId desc')->paginate(input('limit/d'))->toArray();
    }

    /**
     * 获取待结算商家
     */
    public function pageSupplierQuery(){
    	$areaIdPath = input('areaIdPath');
    	$supplierName = input('supplierName');
    	if($supplierName!='')$where[] = ['s.supplierName|s.supplierSn','like','%'.$supplierName.'%'];
    	if($areaIdPath !='')$where[] = ['s.areaIdPath','like',$areaIdPath."%"];
    	$where[] = ['s.dataFlag','=',1];
    	$where[] = ['s.noSettledOrderNum','>',0];
		$page = Db::name('suppliers s')->join('__AREAS__ a2','s.areaId=a2.areaId')
		       ->where($where)
		       ->field('supplierId,supplierSn,supplierName,a2.areaName,supplierkeeper,telephone')
		       ->order('noSettledOrderFee desc')->paginate(input('limit/d'))->toArray();
        $supplierIds = [];
        foreach ($page['data'] as $key => $v) {
            $supplierIds[] = $v["supplierId"];
        }
        $where = [];
        $where[] = ["orderStatus","=",2];
        $where[] = ["supplierId","in",$supplierIds];
        $where[] = ["settlementId","=",0];
        $where[] = ['payType','in',[0,1]];
        $olist = Db::name("supplier_orders")
                ->where($where)
                ->field("supplierId,payType,realTotalMoney,commissionFee,refundedPayMoney")
                ->select();

        $omaps = [];
        $orderNumMaps = [];
        $noSettledOrderFeeMpas = [];
        foreach ($olist as $key => $vo) {
            $backMoney = 0;
            if($vo['payType']==1){
                 //在线支付的返还金额=实付金额+积分抵扣金额-佣金-订单退款金额
                 $backMoney = $vo['realTotalMoney']-$vo['commissionFee']-$vo['refundedPayMoney'];
            }else{
                 //货到付款的返还金额=积分抵扣金额-佣金
                 $backMoney = 0-$vo['commissionFee']-$vo['refundedPayMoney'];
            }
            $omoney = isset($omaps[$vo['supplierId']])?$omaps[$vo['supplierId']]:0;
            $omaps[$vo['supplierId']] = $omoney + $backMoney;
            //未结算订单数
            $noSettledOrderNum = isset($orderNumMaps[$vo['supplierId']])?$orderNumMaps[$vo['supplierId']]:0;
            $orderNumMaps[$vo['supplierId']] = $noSettledOrderNum + 1;
            //未结算佣金
            $noSettledOrderFee = isset($noSettledOrderFeeMpas[$vo['supplierId']])?$noSettledOrderFeeMpas[$vo['supplierId']]:0;
            $noSettledOrderFeeMpas[$vo['supplierId']] = $noSettledOrderFee + $vo['commissionFee'];
        }
        foreach ($page['data'] as $key => $vo) {
            $page['data'][$key]['waitSettlMoney'] = isset($omaps[$vo['supplierId']])?WSTBCMoney($omaps[$vo['supplierId']],0):0;
            $page['data'][$key]['noSettledOrderNum'] = isset($orderNumMaps[$vo['supplierId']])?$orderNumMaps[$vo['supplierId']]:0;
            $page['data'][$key]['noSettledOrderFee'] = isset($noSettledOrderFeeMpas[$vo['supplierId']])?WSTBCMoney($noSettledOrderFeeMpas[$vo['supplierId']],0):0;
        }
        return $page;
	}

   /**
    * 获取商家未结算的订单
    */
   public function pageSupplierOrderQuery(){
   	     $orderNo = input('orderNo');
   	     $payType = (int)input('payType',-1);
         $where[] = ['settlementId','=',0];
         $where[] = ['orderStatus','=',2];
         $where[] = ['supplierId','=',(int)input('id')];
         $where[] = ['dataFlag','=',1];
         if($orderNo!='')$where[] = ['orderNo','like','%'.$orderNo.'%'];
         if(in_array($payType,[0,1]))$where[] = ['payType','=',$payType];
   	     $page = Db::name('supplier_orders')->where($where)
                      ->field('orderId,orderNo,payType,goodsMoney,deliverMoney,realTotalMoney,totalMoney,commissionFee,createTime,
                               refundedPayMoney')
        	          ->order('payType desc,orderId desc')->paginate(input('limit/d'))->toArray();
        if(count($page['data'])>0){
        	foreach ($page['data'] as $key => $v) {
                $backMoney = 0;
                if($v['payType']==1){
                    
                    $surplusMoney = 0;
                    
                    $refundedPayMoney = $v["refundedPayMoney"];
                    
                     //在线支付的返还金额=实付金额-佣金-已退款支付金额
                     $backMoney = $v['realTotalMoney']-$v['commissionFee'] - $refundedPayMoney  - $surplusMoney;
                     $backMoney = WSTBCMoney($backMoney, 0);
                }else{
                     //货到付款的返还金额=积分抵扣金额-佣金
                     $backMoney = 0-$v['commissionFee'];
                }
                $page['data'][$key]['waitSettlMoney'] = $backMoney;
        		$page['data'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
        	}
        }
        return $page;
   }

   
	/**
     * 导出
     */
    public function toExport(){
        $where = [];
        $name='结算申请表';
        $settlementNo = input('settlementNo');
        $startDate = input('startDate');
        $endDate = input('endDate');
        $supplierName = input('supplierName');
        $settlementStatus = (int)input('settlementStatus',-1);
        $sort = input('sort');
        if($startDate!='')$where[] = ['st.createTime','>=',$startDate.' 00:00:00'];
        if($endDate!='')$where[] = ['st.createTime','<=',$endDate.' 23:59:59'];
        if($settlementNo!='')$where[] = ['settlementNo','like','%'.$settlementNo.'%'];
        if($supplierName!='')$where[] = ['supplierName|supplierSn','like','%'.$supplierName.'%']; 
        if($settlementStatus>=0)$where[] = ['settlementStatus','=',$settlementStatus];
        $order = 'st.settlementId desc';
        if($sort){
            $sortArr = explode('.',$sort);
            $order = $sortArr[0].' '.$sortArr[1];
            if($sortArr[0]=='settlementNo'){
                $order = $sortArr[0].'+0 '.$sortArr[1];
            }
        }
        $page = Db::name('supplier_settlements')->alias('st')
                ->join('suppliers s','s.supplierId=st.supplierId','left')
                ->where($where)
                ->field('s.supplierName,settlementNo,settlementId,settlementMoney,commissionFee,backMoney,settlementStatus,settlementTime,st.createTime')
                ->order($order)
                ->select();
       
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("结算");
    
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:G1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '结算单号')
        ->setCellValue('B1', '申请供货商')->setCellValue('C1', '结算金额')
        ->setCellValue('D1', '结算佣金')->setCellValue('E1', '返还金额')
        ->setCellValue('F1', '申请时间')->setCellValue('G1', '状态');
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
        $totalRow = 0;
        for ($row = 0; $row < count($page); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $page[$row]['settlementNo'])
            ->setCellValue('B'.$i, $page[$row]['supplierName'])->setCellValue('C'.$i, '￥'.$page[$row]['settlementMoney'])
            ->setCellValue('D'.$i, '￥'.$page[$row]['commissionFee'])->setCellValue('E'.$i, '￥'.$page[$row]['backMoney'])
            ->setCellValue('F'.$i, $page[$row]['createTime'])->setCellValue('G'.$i, $page[$row]['settlementStatus']==1?'已结算':'未结算');
        }
        $totalRow = count($page)+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:G'.$totalRow)->applyFromArray(array(
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