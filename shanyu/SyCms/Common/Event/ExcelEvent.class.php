<?php
namespace Common\Event;

//Excel导入/导出
class ExcelEvent{
	//excel导入 返回数组
	public function import($file){

		lib("File.PHPExcel");
		$PHPExcel=new \PHPExcel();

		//判断文件后缀引入相应版本的Excel类
		$file_ext=pathinfo($file,PATHINFO_EXTENSION);
		if($file_ext == 'xls'){
			//如果excel文件后缀名为.xls,导入这个类
			$PHPReader=new \PHPExcel_Reader_Excel5();
		}elseif ($file_ext == 'xlsx') {
			//如果excel文件后缀名为.xlsx,导入这下类
			$PHPReader=new \PHPExcel_Reader_Excel2007();
		}else{
			throw new Think\Exception('Excel格式错误!', 0);
		}
		
		//载入文件
		$PHPExcel=$PHPReader->load($file);
		//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
		$currentSheet=$PHPExcel->getSheet(0);
		//获取总列数
		$allColumn=$currentSheet->getHighestColumn();
		//获取总行数
		$allRow=$currentSheet->getHighestRow();
		//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
		for($currentRow=1;$currentRow<=$allRow;$currentRow++){
			//从哪列开始，A表示第一列
			for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
				//数据坐标
				$address=$currentColumn.$currentRow;
				//读取到的数据，保存到数组$arr中
				$arr[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
			}
		
		}
		return $arr;
    }

    //excel导出
    public function export($file_name,$date_tit,$date_con){

		lib("File.PHPExcel");
		$objPHPExcel = new \PHPExcel();
		$objProps = $objPHPExcel->getProperties();

		//设置表头
		$key = ord("A");
		foreach($date_tit as $v){
			$colum = chr($key);
			$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
			$key += 1;
		}

		//设置内容
		$column = 2;
		$objActSheet = $objPHPExcel->getActiveSheet();
	    foreach($date_con as $key => $rows){ 
	    	//行写入
	    	$span = ord("A");
	        foreach($rows as $keyName=>$value){
	        	// 列写入
	        	$j = chr($span);
	        	$objActSheet->setCellValue($j.$column, $value);
	        	$span++;
	        }
	        $column++;
	    }

	    //重命名活动单元表
	   	$objPHPExcel->getActiveSheet()->setTitle($file_name);
	    //设置活动单指数到第一个表,所以Excel打开这是第一个表
	    $objPHPExcel->setActiveSheetIndex(0);

	    //设置保存文件名
		$date = date("Y_m_d",time());
		$file_name .= "_{$date}.xls";
	    $file_name = iconv("utf-8", "gb2312", $file_name);

	    //输出文件
	    header('Content-Type: application/vnd.ms-excel');
	    header("Content-Disposition: attachment;filename=\"$file_name\"");
	    header('Cache-Control: max-age=0');
	    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output'); //文件通过浏览器下载
	    exit;

	}



}