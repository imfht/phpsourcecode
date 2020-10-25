<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class SupplierController extends HomeController {
	//过滤查询字段
	protected $config = array('app_type' => 'common', 'admin' => 'set_tag,tag_manage');

	function _search_filter(&$map) {
		$map['name'] = array('like', "%" . $_POST['name'] . "%");
		$map['letter'] = array('like', "%" . $_POST['letter'] . "%");
		$map['is_del'] = array('eq', '0');
		if (!empty($_POST['tag'])) {
			$map['group'] = $_POST['tag'];
		}
		$map['is_del'] = array('eq', '0');
	}

	function index() {
		$model = M("Supplier");
		$where['is_del'] = 0;
		$list = $model -> where($where) -> select();
		$this -> assign('list', $list);
		$tag_data = D("SystemTag") -> get_data_list();
		$new = array();
		foreach ($tag_data as $val) {
			if(!empty($new[$val['row_id']]))
			{
				$new[$val['row_id']] = $new[$val['row_id']] . $val['tag_id'] . ",";
			}
			else {
				$new[$val['row_id']] = $val['tag_id'] . ",";
			}
			
		}
		$this -> assign('tag_data', $new);
		$this -> _assign_tag_list();
		$this -> display();
		return;
	}

	function export() {
		$model = M("Supplier");
		$where['is_del'] = 0;
		$list = $model -> where($where) -> select();

		Vendor('Excel.PHPExcel');
		//导入thinkphp第三方类库

		// $inputFileName = "Public/templete/Supplier.xlsx";
		// $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel -> getProperties() -> setCreator("smeoa") -> setLastModifiedBy("smeoa") -> setTitle("Office 2007 XLSX Test Document") -> setSubject("Office 2007 XLSX Test Document") -> setDescription("Test document for Office 2007 XLSX, generated using PHP classes.") -> setKeywords("office 2007 openxml php") -> setCategory("Test result file");
		// Add some data
		$i = 1;
		//dump($list);
		foreach ($list as $val) {
			$i++;
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", $val["name"]) -> setCellValue("B$i", $val["short"]) -> setCellValue("C$i", $val["account"]) -> setCellValue("D$i", $val["tax_no"]) -> setCellValue("E$i", $val["payment"]) -> setCellValue("F$i", $val["address"]) -> setCellValue("G$i", $val["contact"]) -> setCellValue("H$i", $val["email"]) -> setCellValue("I$i", $val["office_tel"]) -> setCellValue("J$i", $val["mobile_tel"]) -> setCellValue("J$i", $val["fax"]) -> setCellValue("L$i", $val["im"]) -> setCellValue("M$i", $val["remark"]);
		}
		// Rename worksheet
		$objPHPExcel -> getActiveSheet() -> setTitle('Supplier');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel -> setActiveSheetIndex(0);

		$file_name = "customer.xlsx";
		// Redirect output to a client’s web browser (Excel2007)
		header("Content-Type: application/force-download");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition:attachment;filename =" . str_ireplace('+', '%20', URLEncode($file_name)));
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter -> save('php://output');
		exit ;
	}

	public function import() {

		$opmode = $_POST["opmode"];
		if ($opmode == "import") {
			$File = D('File');
			$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
			$info = $File -> upload($_FILES, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG"));

			if (!$info) {
				$this->error($File -> getError());
			} else {
				//取得成功上传的文件信息
				Vendor('Excel.PHPExcel');
				//导入thinkphp第三方类库
				$inputFileName = C('DOWNLOAD_UPLOAD.rootPath').$info['uploadfile']["savepath"].$info['uploadfile']["savename"];
				$objPHPExcel = \PHPExcel_IOFactory::load($inputFileName);
				$sheetData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
				$model = M("Supplier");
				for ($i = 2; $i <= count($sheetData); $i++) {
					$data = array();
					$data['name'] = $sheetData[$i]["A"];
					$data['short'] = $sheetData[$i]["B"];
					$data['letter'] = get_letter($sheetData[$i]["B"]);
					$data['account'] = $sheetData[$i]["C"];
					$data['tax_no'] = $sheetData[$i]["D"];
					$data['payment'] = $sheetData[$i]["E"];
					$data['address'] = $sheetData[$i]["F"];
					$data['contact'] = $sheetData[$i]["G"];
					$data['email'] = $sheetData[$i]["H"];
					$data['office_tel'] = $sheetData[$i]["I"];
					$data['mobile_tel'] = $sheetData[$i]["J"];
					$data['fax'] = $sheetData[$i]["K"];
					$data['im'] = $sheetData[$i]["L"];
					$data['is_del'] = 0;
					$model -> add($data);
				}
				if (file_exists(__ROOT__ . "/" . $inputFileName)) {
					unlink(__ROOT__ . "/" . $inputFileName);
				}
				$this -> assign('jumpUrl', get_return_url());
				$this -> success('导入成功！');
			}
		} else {
			$this -> display();
		}
	}

	function del($id) {
		$count = $this -> _del($id, CONTROLLER_NAME, true);

		if ($count) {
			$model = D("SystemTag");
			$result = $model -> del_data_by_row($id);
		}

		if ($count !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success("成功删除{$count}条!");
		} else {
			//失败提示
			$this -> error('删除失败!');
		}
	}

	function read($id) {
		$model = M('Supplier');
		$vo = $model -> getById($id);
		$this -> assign('vo', $vo);
		$this -> display();
	}

	function tag_manage() {
		$this -> _system_tag_manage("分组管理");
	}

	function set_tag() {
		$id = $_POST['id'];
		$tag = $_POST['tag'];
		$new_tag = $_POST['new_tag'];
		if (!empty($id)) {
			$model = D("SystemTag");
			$model -> del_data_by_row($id);
			if (!empty($_POST['tag'])) {
				$result = $model -> set_tag($id, $tag);
			}
		};

		if (!empty($new_tag)) {
			$model = D("SystemTag");
			$model -> controller = CONTROLLER_NAME;
			$model -> name = $new_tag;
			$model -> is_del = 0;
			$model -> user_id = get_user_id();
			$new_tag_id = $model -> add();
			if (!empty($id)) {
				$result = $model -> set_tag($id, $new_tag_id);
			}
		};
		if ($result !== false) {//保存成功
			if ($ajax || IS_AJAX)
				$this -> assign('jumpUrl', get_return_url());
			$this -> success('操作成功!');
		} else {
			//失败提示
			$this -> error('操作失败!');
		}
	}

	function json() {
		header("Content-Type:text/html; charset=utf-8");
		$key = $_REQUEST['key'];

		$model = M("Supplier");
		$where['name'] = array('like', "%" . $key . "%");
		$where['letter'] = array('like', "%" . $key . "%");
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['status'] = 1;
		$list = $model -> where($map) -> field('id,name') -> select();
		exit(json_encode($list));
	}

	public function winpop() {
		$node = M("Supplier");
		$menu = array();
		$menu = $node -> where($where) -> field('id,name') -> select();
		$tree = list_to_tree($menu);
		//dump($node);
		$this -> assign('menu', popup_tree_menu($tree));
		$this -> display();
	}

	protected function _insert($name = 'Supplier') {
		$model = D('Supplier');
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> __set('letter', get_letter($model -> __get('name')));
		$model -> __set('user_id', get_user_id());
		//保存当前数据对象
		$list = $model -> add();
		if ($list !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('新增成功!');
		} else {
			//失败提示
			$this -> error('新增失败!');
		}
	}

	protected function _update($name = 'Supplier') {
		$id = $_POST['id'];
		$model = D("Supplier");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> __set('letter', get_letter($model -> __get('name')));
		// 更新数据
		$list = $model -> save();
		if (false !== $list) {
			//成功提示
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('编辑成功!');
		} else {
			//错误提示
			$this -> error('编辑失败!');
		}
	}

	protected function _assign_tag_list() {
		$model = D("SystemTag");
		$tag_list = $model -> get_tag_list('id,name');
		$this -> assign("tag_list", $tag_list);
	}

}
?>