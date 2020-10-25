<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class SignController extends HomeController {
	protected $config = array('app_type' => 'personal');
	//过滤查询字段
	function _search_filter(&$map) {
		if (!empty($_POST["keyword"])) {
			$map['name'] = array('like', "%" . $_POST['keyword'] . "%");
		}
		$map['is_real_time'] = array('eq', 0);
	}

	public function upload() {
		$this -> _upload();
	}

	function read($id) {
		$plugin['baidu_map'] = true;
		$this -> assign("plugin", $plugin);

		$model = M('Sign');
		$vo = $model -> find($id);

		conv_baidu_map($vo['latitude'], $vo['longitude']);

		$this -> assign('vo', $vo);

		$user_info = M("User") -> find($vo['user_id']);
		$this -> assign('user_info', $user_info);

		$this -> display();
	}

	public function report() {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('user_id', get_user_id());

		$auth = $this -> config['auth'];
		$this -> assign('auth', $auth);

		if ($auth['admin']) {
			$menu = array();
			$dept_menu = M("Dept") -> field('id,pid,name') -> where('is_del=0') -> order('sort asc') -> select();
			$dept_tree = list_to_tree($dept_menu, 0);
			$count = count($dept_tree);
			if (empty($count)) {
				/*获取部门列表*/
				$html = '';
				$html = $html . "<option value='{$dept_id}'>{$dept_name}</option>";
				$this -> assign('dept_list', $html);

				/*获取人员列表*/
				$where['is_del'] = array('eq', 0);
				$emp_list = D("User") -> where($where) -> getField('id,name');
				$this -> assign('emp_list', $emp_list);
			} else {
				/*获取部门列表*/
				$this -> assign('dept_list', select_tree_menu($dept_tree));
				$dept_list = tree_to_list($dept_tree);
				$dept_list = rotate($dept_list);
				$dept_list = $dept_list['id'];

				/*获取人员列表*/
				$where['is_del'] = array('eq', 0);
				$emp_list = D("User") -> where($where) -> getField('id,name');
				$this -> assign('emp_list', $emp_list);
			}
		} else {
			$dept_id = get_dept_id();
			$dept_name = get_dept_name();
			$menu = array();
			$dept_menu = M("Dept") -> field('id,pid,name') -> where("is_del=0") -> order('sort asc') -> select();
			$dept_tree = list_to_tree($dept_menu, $dept_id);
			$count = count($dept_tree);
			if (empty($count)) {
				/*获取部门列表*/
				$html = '';
				$html = $html . "<option value='{$dept_id}'>{$dept_name}</option>";
				$this -> assign('dept_list', $html);

				/*获取人员列表*/
				$where['dept_id'] = array('eq', $dept_id);
				$where['is_del'] = array('eq', 0);
				$emp_list = D("User") -> where($where) -> getField('id,name');
				$this -> assign('emp_list', $emp_list);
			} else {
				/*获取部门列表*/
				$this -> assign('dept_list', select_tree_menu($dept_tree));
				$dept_list = tree_to_list($dept_tree);
				$dept_list = rotate($dept_list);
				$dept_list = $dept_list['id'];

				/*获取人员列表*/
				$where['dept_id'] = array('in', $dept_list);
				$where['is_del'] = array('eq', 0);
				$emp_list = D("User") -> where($where) -> getField('id,name');
				$this -> assign('emp_list', $emp_list);
			}
		}

		if ($auth['admin']) {
			// if (empty($map['dept_id'])) {
			// if (!empty($dept_list)) {
			// $map['dept_id'] = array('in', array_merge($dept_list, array($dept_id)));
			// } else {
			// $map['dept_id'] = array('eq', $dept_id);
			// }
			// }
		} else {
			$map['user_id'] = get_user_id();
		}

		$model = D("SignView");
		$map = $this -> _search($model);
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}
		if (I('mode') == 'export') {
			$this -> _report_export($model, $map);
		} else {
			$this -> _list($model, $map);
		}
		$this -> display();
	}

	public function _report_export($model, $map) {
		$list = $model -> where($map) -> select();
		$r = $model -> where($map) -> count();
		if ($r <= 1000) {
			//导入thinkphp第三方类库
			Vendor('Excel.PHPExcel');

			$objPHPExcel = new \PHPExcel();

			$objPHPExcel -> getProperties() -> setCreator("小微OA") -> setLastModifiedBy("小微OA") -> setTitle("Office 2007 XLSX Test Document") -> setSubject("Office 2007 XLSX Test Document") -> setDescription("Test document for Office 2007 XLSX, generated using PHP classes.") -> setKeywords("office 2007 openxml php") -> setCategory("Test result file");
			// Add some data
			$i = 1;

			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", "部门") -> setCellValue("B$i", "姓名") -> setCellValue("C$i", "职位") -> setCellValue("D$i", "类型") -> setCellValue("E$i", "时间") -> setCellValue("F$i", "经度") -> setCellValue("G$i", "纬度") -> setCellValue("H$i", "IP") -> setCellValue("I$i", "地理位置")-> setCellValue("J$i", "备注");

			foreach ($list as $val) {
				$i++;

				$dept_name = $val['dept_name'];
				$name = $val['name'];
				$position_name = $val['position_name'];
				$type = sign_type($val['type']);
				$sign_date = $val['sign_date'];
				$latitude = $val['latitude'];
				$longitude = $val['longitude'];
				$ip = $val['ip'];
				$location = $val['location'];
				$content = $val['content'];

				$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", $dept_name) -> setCellValue("B$i", $name) -> setCellValue("C$i", $position_name) -> setCellValue("D$i", $type) -> setCellValue("E$i", $sign_date) -> setCellValue("F$i", $latitude) -> setCellValue("G$i", $longitude) -> setCellValue("H$i", $ip) -> setCellValue("I$i", $location)-> setCellValue("J$i", $content);
			}

			// Rename worksheet
			$objPHPExcel -> getActiveSheet() -> setTitle('考勤统计');

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel -> setActiveSheetIndex(0);
			$file_name = "考勤统计.xlsx";

			// Redirect output to a client’s web browser (Excel2007)
			header("Content-Type: application/force-download");
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition:attachment;filename =" . str_ireplace('+', '%20', URLEncode($file_name)));
			header('Cache-Control: max-age=0');

			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			//readfile($filename);
			$objWriter -> save('php://output');
			exit ;
		} else {
			header('Content-Type: application/vnd.ms-excel;charset=gbk');
			header('Content-Disposition: attachment;filename="考勤统计.csv"');
			header('Cache-Control: max-age=0');

			$fp = fopen('php://output', 'a');
			$title = array('部门', '姓名', '职位', '类型', '时间', '经度', '纬度', 'IP', '地理位置', '备注');
			foreach ($title as $i => $v) {
				// CSV的Excel支持GBK编码，一定要转换，否则乱码
				$title[$i] = iconv('utf-8', 'gbk', $v);
			}
			fputcsv($fp, $title);
			$cnt = 0;
			
			foreach ($list as $val) {
				$cnt++;
				if (100000 == $cnt) {//刷新一下输出buffer，防止由于数据过多造成问题
					ob_flush();
					flush();
					$cnt = 0;
				}

				$dept_name = $val['dept_name'];
				$name = $val['name'];
				$position_name = $val['position_name'];
				$type = sign_type($val['type']);
				$sign_date = $val['sign_date'];
				$latitude = $val['latitude'];
				$longitude = $val['longitude'];
				$ip = $val['ip'];
				$location = $val['location'];
				$content = $val['content'];
				

				$row = array($dept_name, $name, $position_name, $type, $sign_date, $latitude, $longitude, $ip, $location, $content);

				foreach ($row as $i => $v) {
					$row[$i] = iconv('utf-8', 'gbk', $v);
				}
				fputcsv($fp, $row);
			}
			fclose($fp);
			exit ;
		}
	}

	public function add() {
		$plugin['jquery-ui'] = true;
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$this -> display();
	}

	public function day_view() {
		$this -> index();
	}

	function json() {
		header("Cache-Control: no-cache, must-revalidate");
		header("Content-Type:text/html; charset=utf-8");

		$start_date = $_REQUEST["start_date"];
		$end_date = $_REQUEST["end_date"];

		$where['user_id'] = get_user_id();
		$where['sign_date'] = array( array('egt', $start_date), array('elt', $end_date));

		$list = M("Sign") -> where($where) -> order('sign_date') -> select();
		exit(json_encode($list));
	}

}
?>