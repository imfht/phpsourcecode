<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-2-27
 * Time: 15:15
 */

namespace DataComposer\Providers\excel;

use PHPExcel_IOFactory;
use PHPExcel;

class thinkphp extends _base
{
	public function get($filepath)
	{

		if (!$this->check($filepath)) return [];
		$inputFileType = PHPExcel_IOFactory::identify($filepath);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($filepath);
		//$sheet = $objPHPExcel->getSheet(0);
		$data = $objPHPExcel->getActiveSheet()->toArray();
		$_data = [];
		if ($data && is_array($data)) {
			$title = [];
			foreach ($data as $row) {
				if(!$row)continue;
				if (!$title) {
					$title = $row;
					continue;
				}
				$_row=[];
				foreach ($row as $key=> $cell) {
					if(isset($title[$key])) $_row[$title[$key]]=$cell;
				}
				$_data[]=$_row;
			}
		}

		return $_data;
	}
}