<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class ContactController extends HomeController {
	protected $config = array('app_type' => 'personal');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['user_id'] = array('eq', get_user_id());
		$map['is_del'] = array('eq', '0');
		if (!empty($_POST['tag'])) {
			$map['tag'] = $_POST['tag'];
		}
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$where['name'] = array('like', "%" . $keyword . "%");
			$where['office_tel'] = array('like', "%" . $keyword . "%");
			$where['mobile_tel'] = array('like', "%" . $keyword . "%");
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
		}
	}

	function index() {
		$model = M("Contact");
		$where['user_id'] = array('eq', get_user_id());
		$where['is_del'] = array('eq', '0');
		$list = $model -> where($where) -> select();
		$this -> assign('list', $list);

		$tag_data = D("UserTag") -> get_data_list();
		$new = array();
		foreach ($tag_data as $val) {
			$new[$val['row_id']] = $new[$val['row_id']] . $val['tag_id'] . ",";
		}
		$this -> assign('tag_data', $new);
		$this -> _assign_tag_list();
		$this -> display();
	}

	function export() {
		$model = M("Contact");
		$where['user_id'] = array('eq', get_user_id());
		$where['is_del'] = array('eq', 0);
		$list = $model -> where($where) -> select();

		//导入thinkphp第三方类库
		Vendor('Excel.PHPExcel');

		//$inputFileName = "Public/templete/contact.xlsx";
		// $objPHPExcel = \PHPExcel_IOFactory::load($inputFileName);
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel -> getProperties() -> setCreator("smeoa") -> setLastModifiedBy("smeoa") -> setTitle("Office 2007 XLSX Test Document") -> setSubject("Office 2007 XLSX Test Document") -> setDescription("Test document for Office 2007 XLSX, generated using PHP classes.") -> setKeywords("office 2007 openxml php") -> setCategory("Test result file");
		// Add some data
		$i = 1;
		$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", "姓名") -> setCellValue("B$i", "单位") -> setCellValue("C$i", "部门") -> setCellValue("D$i", "职位") -> setCellValue("E$i", "办公电话") -> setCellValue("F$i", "手机") -> setCellValue("G$i", "邮箱") -> setCellValue("H$i", "QQ") -> setCellValue("I$i", "网站") -> setCellValue("J$i", "地址") -> setCellValue("K$i", "其他");
		//dump($list);
		foreach ($list as $val) {
			$i++;
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", $val["name"]) -> setCellValue("B$i", $val["company"]) -> setCellValue("C$i", $val["dept"]) -> setCellValue("D$i", $val["position"]) -> setCellValue("E$i", $val["office_tel"]) -> setCellValue("F$i", $val["mobile_tel"]) -> setCellValue("G$i", $val["email"]) -> setCellValue("H$i", $val["im"]) -> setCellValue("I$i", $val["website"]) -> setCellValue("J$i", $val["address"]) -> setCellValue("J$i", $val["remark"]);
		}
		// Rename worksheet
		$objPHPExcel -> getActiveSheet() -> setTitle('Contact');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel -> setActiveSheetIndex(0);
		$file_name = "contact.xlsx";
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

	public function import() {
		$opmode = $_POST["opmode"];
		if ($opmode == "import") {
			$File = D('File');
			$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
			$info = $File -> upload($_FILES, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG"));

			if (!$info) {
				$this -> error($File -> getError());
			} else {
				//取得成功上传的文件信息
				Vendor('Excel.PHPExcel');
				//导入thinkphp第三方类库
				$inputFileName = C('DOWNLOAD_UPLOAD.rootPath') . $info['uploadfile']["savepath"] . $info['uploadfile']["savename"];

				$objPHPExcel = \PHPExcel_IOFactory::load($inputFileName);
				$sheetData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
				$model = M("Contact");
				for ($i = 2; $i <= count($sheetData); $i++) {
					$data = array();
					$data['name'] = $sheetData[$i]["A"];
					$data['company'] = $sheetData[$i]["B"];
					$data['letter'] = get_letter($sheetData[$i]["A"]);
					$data['dept'] = $sheetData[$i]["C"];
					$data['position'] = $sheetData[$i]["D"];
					$data['email'] = $sheetData[$i]["G"];
					$data['office_tel'] = $sheetData[$i]["E"];
					$data['mobile_tel'] = $sheetData[$i]["F"];
					$data['website'] = $sheetData[$i]["I"];
					$data['im'] = $sheetData[$i]["H"];
					$data['address'] = $sheetData[$i]["J"];
					$data['user_id'] = get_user_id();
					$data['remark'] = $sheetData[$i]["K"];
					$data['is_del'] = 0;
					$model -> add($data);
				}
				//dump($sheetData);
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

	function tag_manage() {
		$this -> _user_tag_manage("分组管理");
	}

	function del($id) {
		$count = $this -> _del($id, CONTROLLER_NAME, true);

		if ($count) {
			$model = D("UserTag");
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

	function set_tag() {
		$id = $_POST['id'];
		$tag = $_POST['tag'];
		$new_tag = $_POST['new_tag'];
		if (!empty($id)) {
			$model = D("UserTag");
			$model -> del_data_by_row($id);
			if (!empty($_POST['tag'])) {
				$result = $model -> set_tag($id, $tag);
			}
		};

		if (!empty($new_tag)) {
			$model = D("UserTag");
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

	protected function _insert($name = 'Contact') {
		$model = D('Contact');
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> letter = get_letter($model -> name);
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

	protected function _update($name = 'Contact') {
		$id = $_POST['id'];
		$model = D("Contact");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> letter = get_letter($model -> name);
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
		$model = D("UserTag");
		$tag_list = $model -> get_tag_list('id,name');
		$this -> assign("tag_list", $tag_list);
	}

}
?>