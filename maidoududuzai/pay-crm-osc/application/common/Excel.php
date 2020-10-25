<?php

namespace app\common;

use \think\Db;

require_once EXTEND_PATH . 'PHPExcel/PHPExcel.php';
require_once EXTEND_PATH . 'PHPExcel/PHPExcel/IOFactory.php';

Class Excel {

	public static function Reader($xls_file = null)
	{
		$Excel = new \PHPExcel();
		$Reader = new \PHPExcel_Reader_Excel5();
		$xls_data = [];
		if(file_exists($xls_file)) {
			$Excel = $Reader->load($xls_file);
			$Sheet = $Excel->getSheet(0);
			$allRow = $Sheet->getHighestRow();
			$allColumn = $Sheet->getHighestColumn();
			for($r=1; $r<=$allRow; $r++) {
				for($c='A'; $c<=$allColumn; $c++) {
					$address = $c . $r;
					$xls_data[$r][$c] = trim((string)$Sheet->getCell($address)->getValue());
				}
			}
		}
		return $xls_data;
	}

	public static function Writer($data = [], $SheetIndex = 0, $SheetTitle = null)
	{
		$_Key = [];
		foreach(range('A', 'Z') as $val) {
			$_Key[] = $val;
		}
		$Excel = new \PHPExcel();
		foreach($data as $key => $val) {
			if($key == 0) {
				continue;
			}
			$i = 0;
			foreach($val as $value) {
				$Excel->setActiveSheetIndex(0)->setCellValueExplicit($_Key[$i] . $key, (string)$value, 's');
				$i++;
			}
		}
		header('Cache-Control: cache, must-revalidate');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="Excel5.xls"');
		$Writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
		$Writer->save('php://output');
	}

}

