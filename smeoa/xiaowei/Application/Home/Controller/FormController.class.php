<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class FormController extends HomeController {
	protected $config = array('app_type' => 'folder','admin' => 'del,move_to,folder_manage,field_type,field_manage');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		$keyword = I('keyword');
		if (!empty($keyword) && empty($map['64'])) {
			$map['name'] = array('like', "%" . $keyword . "%");
		}
	}

	public function index() {

		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$folder_list = D("SystemFolder") -> get_authed_folder();
		if (!empty($folder_list)) {
			$map['folder'] = array("in", $folder_list);
		} else {
			$map['_string'] = '1=2';
		}

		$model = D("FormView");

		if (!empty($model)) {
			$this -> _list($model, $map);
		}
		$this -> display();
	}

	public function edit($id) {
		$plugin['uploader'] = true;
		$plugin['date'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$model = M("Form");
		$folder_id = $model -> where("id=$id") -> getField('folder');
		$this -> assign("auth", D("SystemFolder") -> get_folder_auth($folder_id));

		$vo = $model -> find($id);
		if (empty($vo)) {
			$this -> error("系统错误");
		}
		$this -> assign("vo", $vo);
		$field_list = D("UdfField") -> get_data_list($vo['udf_data']);
		$this -> assign("field_list", $field_list);
		$this -> display();
	}

	public function folder($fid) {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('auth', $this -> config['auth']);

		$model = D("Form");
		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$map['folder'] = $fid;
		if (I('mode') == 'export') {
			$this -> _folder_export($model, $map);
		} else {
			$list = $this -> _list($model, $map);

		}

		$udf_data = $list[0]['udf_data'];
		if (!empty($udf_data)) {
			$udf_field = D("UdfField") -> get_show_field($udf_data);
			$this -> assign('udf_field', $udf_field);
		}

		$where = array();
		$where['id'] = array('eq', $fid);

		$folder_name = M("SystemFolder") -> where($where) -> getField("name");
		$this -> assign("folder_name", $folder_name);
		$this -> assign("folder", $fid);

		$this -> display();
		return;
	}

	private function _folder_export($model, $map) {
		$list = $model -> where($map) -> select();
		$list_one = $model -> where($map) -> find();
		$model_flow_field = D("UdfField");
		$topcell = $model_flow_field -> get_field_name($list_one["udf_data"]);

		//导入thinkphp第三方类库
		Vendor('Excel.PHPExcel');

		//$inputFileName = "Public/templete/contact.xlsx";
		//$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		$objPHPExcel = new \PHPExcel();

		$objPHPExcel -> getProperties() -> setCreator("小微OA") -> setLastModifiedBy("小微OA") -> setTitle("Office 2007 XLSX Test Document") -> setSubject("Office 2007 XLSX Test Document") -> setDescription("Test document for Office 2007 XLSX, generated using PHP classes.") -> setKeywords("office 2007 openxml php") -> setCategory("Test result file");
		// Add some data

		$i = 1;
		$t = E;
		//dump($list);

		//编号，类型，标题，登录时间，部门，登录人，状态，审批，协商，抄送，审批情况，自定义字段
		$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", "编号") -> setCellValue("B$i", "标题") -> setCellValue("C$i", "登录人") -> setCellValue("D$i", "登录时间") -> setCellValue("E$i", "内容");

		foreach ($topcell as $val) {
			$t++;
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("$t$i", $val);

		}
		foreach ($list as $val) {
			$i++;
			//dump($val);

			$doc_no = $val["doc_no"];
			//编号
			$name = $val["name"];
			//标题
			$user_name = $val["user_name"];
			//登记人
			$create_time = $val["create_time"];
			$create_time = to_date($val["create_time"], 'Y-m-d H:i:s');
			//创建时间
			$content = $val["content"];

			//编号，类型，标题，登录时间，部门，登录人，状态，审批，协商，抄送，审批情况，自定义字段
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", $doc_no) -> setCellValue("B$i", $name) -> setCellValue("C$i", $user_name) -> setCellValue("D$i", $create_time) -> setCellValue("E$i", ' ' . $content);

			$field_list = $model_flow_field -> get_data_list($val["udf_data"]);
			//	dump($field_list);
			$k = 0;
			if (!empty($field_list)) {
				foreach ($field_list as $field) {
					$k++;
					$field_data = $field['val'];
					$location = get_cell_location("E", $i, $k);
					$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue($location, ' ' . $field_data);
				}
			}
		}
		// Rename worksheet
		$objPHPExcel -> getActiveSheet() -> setTitle('报表统计');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel -> setActiveSheetIndex(0);
		$file_name = "报表统计.xlsx";
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

	public function add($fid) {
		$plugin['uploader'] = true;
		$plugin['date'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$model_flow_field = D("UdfField");
		$field_list = $model_flow_field -> get_field_list($fid);
		$this -> assign("field_list", $field_list);

		$this -> assign('folder', $fid);
		$this -> display();
	}

	public function add_folder() {
		$this -> _system_folder_manage('报表管理', true);
	}

	public function read($id) {
		$model = M("Form");
		$folder_id = $model -> where("id=$id") -> getField('folder');
		$this -> assign("auth", D("SystemFolder") -> get_folder_auth($folder_id));

		$vo = $model -> find($id);
		if (empty($vo)) {
			$this -> error("系统错误");
		}
		$this -> assign('vo', $vo);
		$field_list = D("UdfField") -> get_data_list($vo['udf_data']);
		$this -> assign("field_list", $field_list);
		$this -> display();
	}

	public function del($id) {
		$where['id'] = array('in', $id);
		$folder = M("Form") -> distinct(true) -> where($where) -> getField('folder', true);
		if (count($folder) == 1) {
			$auth = D("SystemFolder") -> get_folder_auth($folder[0]);
			if ($auth['admin'] == true) {
				$this -> _del($id);
			}
		} else {
			$return['info'] = "删除失败";
			$return['status'] = 0;
			$this -> ajaxReturn($return);
		}
	}

	/** 插入新新数据  **/
	protected function _insert($name = CONTROLLER_NAME) {

		$model = D($name);
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}

		$model -> udf_data = D('UdfField') -> get_field_data();

		$list = $model -> add();

		if ($list !== false) {//保存成功
			//$flow_filed = D("UdfField") -> set_field($list);
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('新增成功!');
		} else {
			$this -> error('新增失败!');
			//失败提示
		}
	}

	/* 更新数据  */
	protected function _update($name = CONTROLLER_NAME) {
		$model = D($name);
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> udf_data = D('UdfField') -> get_field_data();
		$list = $model -> save();
		if (false !== $list) {
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('编辑成功!');
			//成功提示
		} else {
			$this -> error('编辑失败!');
			//错误提示
		}
	}

	function folder_manage() {
		$this -> _system_folder_manage('报表管理', true);
	}

	function upload() {
		$this -> _upload();
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}

	function field_type() {
		$field_type_list = D("SystemFolder") -> get_folder_list();
		trace($field_type_list);
		$this -> assign("list", $field_type_list);
		$this -> display();
	}

	function field_manage($row_type) {
		$this -> assign("folder_name", "自定义字段管理");
		$this -> _field_manage($row_type);
	}

}
