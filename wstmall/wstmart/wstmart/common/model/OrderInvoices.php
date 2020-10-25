<?php
namespace wstmart\common\model;
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
 * 发票详情类
 */
class OrderInvoices extends Base{
    protected $table = 'wst_orders';
    protected $pk = 'orderId';

    /************************************* 商家 *********************************************/
    /**
     * 获取发票详情列表列表
     */
    public function queryShopInvoicesByPage(){
        $orderNo = (int)input('orderNo');
        $startDate = input('startDate');
        $endDate = input('endDate');
        $isMakeInvoice = (int)input('isMakeInvoice');
        $where = [];
        if($orderNo!='') $where[] = ['o.orderNo','like',"%$orderNo%"];
        if($startDate!='' && $endDate!=''){
            $where[] = ['o.createTime','between',[$startDate.' 00:00:00',$endDate.' 23:59:59']];
        }else if($startDate!=''){
            $where[] = ['o.createTime','>=',$startDate.' 00:00:00'];
        }else if($endDate!=''){
            $where[] = ['o.createTime','<=',$endDate.' 23:59:59'];
        }
        $where[] = ['o.shopId','=',(int)session('WST_USER.shopId')];
        $where[] = ['o.dataFlag','=',1];
        $where[] = ['o.isInvoice','=',1];
        $where[] = ['o.isMakeInvoice','=',$isMakeInvoice];
        $where[] = ['o.orderStatus','not in','-1,-2'];
        $rs = $this->alias('o')
            ->field('o.orderNo, o.realTotalMoney, o.invoiceJson, o.createTime, o.orderId')
            ->where($where)
            ->order('o.orderId desc')
            ->paginate(input('post.limit/d'))
            ->toArray();

        if(count($rs)>0){
            foreach ($rs['data'] as $k=>$v){
                $result = json_decode($v['invoiceJson'],true);
                $rs['data'][$k]['invoiceHead'] = $result['invoiceHead'];
                if(isset($result['invoiceCode']) )  $rs['data'][$k]['invoiceCode'] = $result['invoiceCode'];
            }
        }
        return WSTReturn('ok',1,$rs);
    }

    /**
     * 导出发票
     */
    public function toExport(){
        $name='invoice';
        $where = [];
        $ids = input('ids');
        if($ids != ''){
            $ids = explode(',',WSTFormatIn(',',input('ids')));
            $where[] = ['o.orderId','in',$ids];
        }
        $isMakeInvoice = (int)input('isMakeInvoice');
        $where[] = ['o.isMakeInvoice','=',$isMakeInvoice];
        $where[] = ['o.dataFlag','=',1];
        $where[] = ['o.shopId','=',(int)session('WST_USER.shopId')];
        $where[] = ['o.orderStatus','not in','-1,-2'];
        $where[] = ['o.isInvoice','=',1];

        $page = $this->alias('o')
            ->field('o.orderNo, o.realTotalMoney, o.invoiceJson, o.createTime, o.orderId ,o.isInvoice')
            ->where($where)
            ->order('o.createTime', 'desc')
            ->paginate(input('post.limit/d'))
            ->toArray();

        if(count($page['data'])>0){
            foreach ($page['data'] as $key => $v){
                    $invoiceArr = json_decode($v['invoiceJson'],true);
                    $page['data'][$key]['invoiceHead'] = $invoiceArr['invoiceHead'];
                    if(isset($invoiceArr['invoiceCode'])){
                        $page['data'][$key]['invoiceCode'] = $invoiceArr['invoiceCode'];
                    }else{
                        $page['data'][$key]['invoiceCode'] = '';
                    }
                $page['data'][$key]['realTotalMoney'] = $v['realTotalMoney'];
                $page['data'][$key]['createTime'] = $v['createTime'];
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);

        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:E1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', '订单编号')->setCellValue('B1', '开票金额')->setCellValue('C1', '发票抬头')->setCellValue('D1', '发票税号')
            ->setCellValue('E1', '创建时间');
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($page['data']); $row++){
            $i = $i+1;
            $i2 = $i3 = $i;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, $page['data'][$row]['orderNo'])->setCellValue('B'.$i2, $page['data'][$row]['realTotalMoney'])->setCellValue('C'.$i2, " ".$page['data'][$row]['invoiceHead'])->setCellValue('D'.$i2, " ".$page['data'][$row]['invoiceCode'])->setCellValue('E'.$i2, $page['data'][$row]['createTime']);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }
        $totalRow = ($totalRow==0)?1:$totalRow-1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:E'.$totalRow)->applyFromArray(array(
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
     * 批量设置
     */
    public function setByBatch(){
        $shopId = (int)session('WST_USER.shopId');
        $ids = explode(',',WSTFormatIn(',',input('post.ids')));
        $isMakeInvoice = (int)input('isMakeInvoice');
        $data = [];
        $data['o.isMakeInvoice'] = $isMakeInvoice;
        Db::startTrans();
        try{
            $result = $this->alias('o')
                ->where([['o.shopId','=',$shopId],['o.orderId','in',$ids],['o.dataFlag','=',1],['o.orderStatus','not in','-1,-2']])
                ->update($data);
            if(false !== $result){
                Db::commit();
                return WSTReturn("设置成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('设置失败',-1);
        }
    }
}
