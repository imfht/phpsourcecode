<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class UdfFieldController extends HomeController {
	protected $config = array('app_type' => 'asst');

	function add($controller) {
		$this -> assign('controller', $controller);
		$row_type = I('row_type');
		$this -> assign('row_type', $row_type);
		$this -> display();
	}

	function index($row_type) {
		if (IS_POST) {
			$opmode = I("opmode");
			$model = D("UdfField");
			if (false === $model -> create()) {
				$this -> error($model -> getError());
			}
			if ($opmode == "add") {
				$list = $model -> add();
				if ($list !== false) {//保存成功
					$this -> assign('jumpUrl', get_return_url());
					$this -> success('新增成功!');
				} else {
					$this -> error('新增失败!');
					//失败提示
				}
			}
			if ($opmode == "edit") {
				$list = $model -> save();
				if ($list !== false) {//保存成功
					$this -> assign('jumpUrl', get_return_url());
					$this -> success('保存成功!');
				} else {
					$this -> error('保存失败!');
					//失败提示
				}
			}
			if ($opmode == "del") {
				$id = $_REQUEST['id'];
				$list = $model -> where("id=$id") -> delete();
				if ($list !== false) {//保存成功
					$this -> assign('jumpUrl', get_return_url());
					$this -> success('删除成功!');
				} else {
					$this -> error('删除失败!');
					//失败提示
				}
			}
		}

		$plugin['editor'] = true;
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$this -> assign("plugin", $plugin);

		$controller = CONTROLLER_NAME;
		$this -> assign('controller', CONTROLLER_NAME);
		$model = D("UdfField");

		$this -> assign('row_type', $row_type);

		$where['row_type'] = array('eq', $row_type);
		$where['is_del'] = 0;
		$where['controller'] = $controller;

		$field_list = $model -> where($where) -> order('sort asc') -> select();

		//$tree = list_to_tree($field_list);
		$this -> assign('menu', sub_tree_menu($field_list));

		$this -> assign("field_list", $field_list);
		$this -> display("UdfField:index");
	}

	public function import() {
		$row_type = I('request.row_type');
		$controller = I('request.controller');
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File -> upload($_FILES, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG"));
		if (!$info) {
			$this -> error();
		} else {
			//取得成功上传的文件信息
			Vendor('Excel.PHPExcel');
			//导入thinkphp第三方类库

			$import_file = $info['file']["path"];
			$import_file = substr($import_file, 1);

			$objPHPExcel = \PHPExcel_IOFactory::load($import_file);

			$sheetData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
			$model = D("UdfField");
			for ($i = 1; $i <= count($sheetData); $i++) {
				$data = array();
				$data['row_type'] = $row_type;
				$data['name'] = $sheetData[$i]["A"];
				$data['sort'] = $sheetData[$i]["B"];
				$data['msg'] = $sheetData[$i]["C"];
				$data['type'] = $sheetData[$i]["D"];
				$data['layout'] = $sheetData[$i]["E"];
				$data['data'] = $sheetData[$i]["F"];
				$data['validate'] = $sheetData[$i]["G"];
				$data['config'] = $sheetData[$i]["H"];
				$data['controller'] = $controller;
				$user_id = $model -> add($data);
			}
			//dump($sheetData);
			$return['status'] = 1;
			$return['info'] = '导入成功';
			$this -> ajaxReturn($return);
		}
	}

	function export($row_type) {
		$model = M("UdfField");
		$where['row_type'] = array('eq', $row_type);
		$list = $model -> where($where) -> select();

		//导入thinkphp第三方类库
		Vendor('Excel.PHPExcel');

		$objPHPExcel = new \PHPExcel();
		$objPHPExcel -> getProperties() -> setCreator("smeoa") -> setLastModifiedBy("smeoa") -> setTitle("Office 2007 XLSX Test Document") -> setSubject("Office 2007 XLSX Test Document") -> setDescription("Test document for Office 2007 XLSX, generated using PHP classes.") -> setKeywords("office 2007 openxml php") -> setCategory("Test result file");
		// Add some data
		$i = 0;

		foreach ($list as $val) {
			$i++;
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", $val["name"]) -> setCellValue("B$i", $val["sort"]) -> setCellValue("C$i", $val["msg"]) -> setCellValue("D$i", $val["type"]) -> setCellValue("E$i", $val["layout"]) -> setCellValue("F$i", $val["data"]) -> setCellValue("G$i", $val["validate"]) -> setCellValue("H$i", $val["config"]);
		}
		// Rename worksheet
		$objPHPExcel -> getActiveSheet() -> setTitle('Contact');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel -> setActiveSheetIndex(0);
		$file_name = "udf_field.xlsx";
		// Redirect output to a client’s web browser (Excel2007)
		header("Content-Type: application/force-download");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition:attachment;filename =" . str_ireplace('+', '%20', URLEncode($file_name)));
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//readfile($filename);
		$objWriter -> save('php://output');
		exit ;
	}

}
