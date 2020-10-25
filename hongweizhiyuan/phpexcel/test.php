<?php  
error_reporting(E_ALL);  
//date_default_timezone_set('Europe/London');  
/** PHPExcel */  
require_once '../common/phpexcel/classes/PHPExcel.php';  
  
// Create new PHPExcel object  
$objPHPExcel = new PHPExcel();  
  
// Set properties  
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")  
                             ->setLastModifiedBy("Maarten Balliauw")  
                             ->setTitle("Office 2007 XLSX Test Document")  
                             ->setSubject("Office 2007 XLSX Test Document")  
                             ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")  
                             ->setKeywords("office 2007 openxml php")  
                             ->setCategory("Test result file");  
$objPHPExcel->getActiveSheet()->mergeCells('A1:O1');  
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);  
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);  
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);  
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);  
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);  
//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);  
 
  
// Add some data  
  
  
$objPHPExcel->setActiveSheetIndex(0)  
            ->setCellValue('A1', '山西省高考报考资料订购平台')  
            ->setCellValue('A2', 'ID')  
            ->setCellValue('B2', '订单号')  
            ->setCellValue('C2', '书籍金额')  
            ->setCellValue('D2', '运费')  
            ->setCellValue('E2', '总金额')  
            ->setCellValue('F2', '下单人')  
            ->setCellValue('G2', '下单人类型')
            ->setCellValue('H2', '下单时间')
            ->setCellValue('I2', '付款时间')
            ->setCellValue('J2', '支付方式')
            ->setCellValue('K2', '配送方式')
            ->setCellValue('L2', '订单状态')
            ->setCellValue('M2', '收货信息')
            ->setCellValue('N2', '支付宝付款单号')
            ->setCellValue('O2', '数量')
    		;      
  
//数据库连接  
$db = mysql_connect("localhost", "root", "bkzldgcom!@#");  
mysql_select_db("lyshop",$db);  //选择数据库，这里为"ywcl"。  
mysql_query("SET NAMES UTF8"); //设定编码方式为UTF8  
  
$sqlgroups="select a.* ,b.cid,b.schoolprov,b.schoolcity,b.schooldist,b.school,b.schoolclass,b.address from n_orders as a left join n_users as b on a.userid=b.id where 1=1 order by a.orderid desc limit 0,5";
$resultgroups=mysql_query($sqlgroups);  
    $numrows=mysql_num_rows($resultgroups);  
	//print_r($numrows);
	//exit;
    if ($numrows>0)  
    {  
        $count=2;  
        while($data=mysql_fetch_array($resultgroups))  
        {  
            $count+=1;  
            $l01="A"."$count";  
            $l02="B"."$count";  
            $l03="C"."$count";  
            $l04="D"."$count";  
            $l05="E"."$count";  
            $l06="F"."$count";  
            $l07="G"."$count"; 
            $l08="H"."$count"; 
            $l09="I"."$count"; 
            $l10="J"."$count"; 
            $l11="K"."$count"; 
            $l12="L"."$count"; 
            $l13="M"."$count"; 
            $l14="N"."$count"; 
            $l15="O"."$count"; 
			echo $data['cid'];
			echo "<hr/>";
		
			//下单人类型
			if ($data['cid'] = 3) { $data['cid']= "学生";}
			else if ($data['cid'] = 1) {$data['cid']= "社会人士";}
			else if ($data['cid'] = 2) {$data['cid']= "学校";}
				
			//下单时间
			$paytime=date("Y-m-d H:i", $data['paytime']);
			
			
            $objPHPExcel->setActiveSheetIndex(0)              
                        ->setCellValue($l01, $data['orderid'])  
                        ->setCellValue($l02, $data['ordersn'])  
                        ->setCellValue($l03, $data['total_fee'])  
                        ->setCellValue($l04, $data['freight'])  
                        ->setCellValue($l05, $data['total_fee'])  
                        ->setCellValue($l06, $data['usertruename'])  
                        ->setCellValue($l07, $data['cid']) 
                        ->setCellValue($l08, $paytime) 
                        ->setCellValue($l09, $data['payedtime']) 
                        ->setCellValue($l10, $data['payment']) 
                        ->setCellValue($l11, $data['delivery']) 
                        ->setCellValue($l12, $data['orderstatus']) 
                        ->setCellValue($l13, $data['schoolprov']) 
                        ->setCellValue($l14, $data['alipayordersn']) 
                        ->setCellValue($l15, $data['carttotal']) 
						;  
        }  
    }          
// Rename sheet  
$objPHPExcel->getActiveSheet()->setTitle('山西省高考报考资料订购平台');  
  
  
// Set active sheet index to the first sheet, so Excel opens this as the first sheet  
$objPHPExcel->setActiveSheetIndex(0);  
  
  
// Redirect output to a client’s web browser (Excel5)  
header('Content-Type: application/vnd.ms-excel');  
$filename = date("Y-m-d");
ob_end_clean(); 
header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
header('Cache-Control: max-age=0');  
  
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
$objWriter->save('php://output');  
exit;  