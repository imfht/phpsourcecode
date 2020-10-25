<?php
namespace wstmart\shop\model;
use wstmart\common\model\Reports as CReports;
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
 * 报表类
 */
class Reports extends CReports{

    /**
     * 获取商品销售排行
     */
    public function getTopSaleGoods($sId=0){
        $startDate=input('startDate');
        $endDate=input('endDate');
        if(empty($startDate)&&empty($endDate)){
            $start=date('Y-m-d 00:00:00',strtotime("-1 months"));
            $end=date('Y-m-d 23:59:59');
        }else{
            $start = date('Y-m-d 00:00:00',strtotime($startDate));
            $end = date('Y-m-d 23:59:59',strtotime($endDate));
        }
        $shopId = ($sId==0)?(int)session('WST_USER.shopId'):$sId;
        $prefix = config('database.prefix');
        $rs = Db::table($prefix.'order_goods')->alias([$prefix.'order_goods'=>'og',$prefix.'orders'=>'o',$prefix.'goods'=>'g'])
            ->join($prefix.'orders','og.orderId=o.orderId')
            ->join($prefix.'goods','og.goodsId=g.goodsId')
            ->order('goodsNum desc')
            ->whereTime('o.createTime','between',[$start,$end])
            ->where('(payType=0 or (payType=1 and isPay=1)) and o.dataFlag=1 and o.shopId='.$shopId)->group('og.goodsId')
            ->field('og.goodsId,g.goodsName,goodsSn,sum(og.goodsNum) goodsNum,g.goodsImg')
            ->paginate(input('limit/d'))->toArray();
        return $rs;
    }

    /**
     * 导出商品销售排行Excel
     */
    public function toExportTopSaleGoods(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $prefix = config('database.prefix');
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $shopId = (int)session('WST_USER.shopId');
        $prefix = config('database.prefix');
        $rs = Db::table($prefix.'order_goods')->alias([$prefix.'order_goods'=>'og',$prefix.'orders'=>'o',$prefix.'goods'=>'g'])
          ->join($prefix.'orders','og.orderId=o.orderId')
          ->join($prefix.'goods','og.goodsId=g.goodsId')
          ->order('goodsNum desc')
          ->whereTime('o.createTime','between',[$start,$end])
          ->where('(payType=0 or (payType=1 and isPay=1)) and o.dataFlag=1 and o.shopId='.$shopId)->group('og.goodsId')
          ->field('og.goodsId,g.goodsName,goodsSn,sum(og.goodsNum) goodsNum,g.goodsImg')
          ->select();
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("商品销售排行");//种类
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:C1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', '商品名称')
        ->setCellValue('B1', '商品编号')
        ->setCellValue('C1', '销量');
        $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs[$row]['goodsName'])
            ->setCellValue('B'.$i, " ".$rs[$row]['goodsSn'])
            ->setCellValue('C'.$i, $rs[$row]['goodsNum']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:C'.$totalRow)->applyFromArray(array(
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
     * 销售额统计Excel
     */
    public function toExportStatSales(){
        $name = 'report';
        $rdata = $this->getStatSales();
        $rs = empty($rdata)?[]:$rdata['data'];
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("销售额统计");//种类
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:C1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', '日期')
        ->setCellValue('B1', '订单数')
        ->setCellValue('C1', '销售额');
        $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($rs['list']); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs['list'][$row]['day'])
            ->setCellValue('B'.$i, $rs['list'][$row]['num'])
            ->setCellValue('C'.$i, $rs['list'][$row]['val']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:C'.$totalRow)->applyFromArray(array(
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
     * 订单统计导出Excel
     */
    public function toExportStatOrders(){
        $name = 'report';
        $rdata = $this->getStatOrders();
        $rs = empty($rdata)?[]:$rdata['data'];
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("销售额统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array('font' => array('bold' => true,'color'=>array('argb' => 'ffffffff')));
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:F1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', '日期')
        ->setCellValue('B1', '待付款订单')
        ->setCellValue('C1', '取消订单')
        ->setCellValue('D1', '拒收订单')
        ->setCellValue('E1', '正常订单')
        ->setCellValue('F1', '总订单');
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($rs['list']); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs['list'][$row]['day'])
            ->setCellValue('B'.$i, $rs['list'][$row]['of2'])
            ->setCellValue('C'.$i, $rs['list'][$row]['o1'])
            ->setCellValue('D'.$i, $rs['list'][$row]['o3'])
            ->setCellValue('E'.$i, $rs['list'][$row]['ou'])
            ->setCellValue('F'.$i, $rs['list'][$row]['of2']+$rs['list'][$row]['o1']+$rs['list'][$row]['o3']+$rs['list'][$row]['ou']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:F'.$totalRow)->applyFromArray(array(
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