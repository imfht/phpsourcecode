<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class FlowController extends HomeController {
	protected $config = array('app_type' => 'common', 'read' => 'approve,mark,field_manage,back_to,reject,send_refer,refer');

	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$map['name'] = array('like', "%" . $keyword . "%");
		}
	}

	function index() {
		$model = D('FlowTypeView');
		$where['is_del'] = 0;
		$user_id = get_user_id();
		$role_list = D("Role") -> get_role_list($user_id);
		$role_list = rotate($role_list);
		$role_list = $role_list['role_id'];

		$duty_list = D("Role") -> get_duty_list($role_list);
		$duty_list = rotate($duty_list);
		$duty_list = $duty_list['duty_id'];

		if (!empty($duty_list)) {
			$where['request_duty'] = array('in', $duty_list);
		} else {
			$where['_string'] = '1=2';
		}

		$list = $model -> where($where) -> order('sort') -> select();
		$this -> assign("list", $list);
		$this -> _assign_tag_list();
		$this -> display();
	}

	function _flow_auth_filter($folder, &$map) {
		$emp_no = get_emp_no();
		$user_id = get_user_id();
		switch ($folder) {
			case 'confirm' :
				$this -> assign("folder_name", '待审批');
				$FlowLog = M("FlowLog");
				$where['emp_no'] = $emp_no;
				$where['is_del'] = 0;
				$where['_string'] = "result is null";
				$log_list = $FlowLog -> where($where) -> field('flow_id') -> select();
				$log_list = rotate($log_list);
				
				if (!empty($log_list)) {
					$map['id'] = array('in', $log_list['flow_id']);
				} else {
					$map['_string'] = '1=2';
				}
				break;

			case 'darft' :
				$this -> assign("folder_name", '草稿箱');
				$map['user_id'] = $user_id;
				$map['step'] = 10;
				break;

			case 'submit' :
				$this -> assign("folder_name", '已提交');
				$map['user_id'] = array('eq', $user_id);
				$map['step'] = array( array('gt', 10), array('eq', 0), 'or');

				break;

			case 'finish' :
				$this -> assign("folder_name", '已审批');
				$FlowLog = M("FlowLog");
				$where['emp_no'] = $emp_no;
				$where['is_del'] = 0;
				$where['_string'] = "result is not null";
				$log_list = $FlowLog -> where($where) -> field('flow_id') -> select();
				$log_list = rotate($log_list);
				if (!empty($log_list)) {
					$map['id'] = array('in', $log_list['flow_id']);
				} else {
					$map['_string'] = '1=2';
				}
				break;

			case 'receive' :
				$this -> assign("folder_name", '参阅箱');
				$FlowLog = M("FlowLog");
				$where['emp_no'] = $emp_no;
				$where['step'] = 100;
				$where['is_del'] = 0;
				$log_list = $FlowLog -> where($where) -> field('flow_id') -> select();
				$log_list = rotate($log_list);
				if (!empty($log_list)) {
					$map['id'] = array('in', $log_list['flow_id']);
				} else {
					$map['_string'] = '1=2';
				}
				break;

			case 'receive_read' :
				$this -> assign("folder_name", '已参阅');
				$FlowLog = M("FlowLog");
				$where['emp_no'] = $emp_no;
				$where['step'] = 100;
				$where['is_del'] = 0;
				$where['_string'] = "comment is not null";
				$log_list = $FlowLog -> where($where) -> field('flow_id') -> select();
				$log_list = rotate($log_list);
				if (!empty($log_list)) {
					$map['id'] = array('in', $log_list['flow_id']);
				} else {
					$map['_string'] = '1=2';
				}
				break;
				
			case 'receive_unread' :
				$this -> assign("folder_name", '未参阅');
				$FlowLog = M("FlowLog");
				$where['emp_no'] = $emp_no;
				$where['step'] = 100;
				$where['is_del'] = 0;
				$where['_string'] = "comment is null";
				$log_list = $FlowLog -> where($where) -> field('flow_id') -> select();
				$log_list = rotate($log_list);
				if (!empty($log_list)) {
					$map['id'] = array('in', $log_list['flow_id']);
				} else {
					$map['_string'] = '1=2';
				}
				break;	
							
			case 'report' :
				$this -> assign("folder_name", '统计报告');
				$role_list = D("Role") -> get_role_list($user_id);
				$role_list = rotate($role_list);
				$role_list = $role_list['role_id'];

				$duty_list = D("Role") -> get_duty_list($role_list);
				$duty_list = rotate($duty_list);
				$duty_list = $duty_list['duty_id'];

				if (!empty($duty_list)) {
					$map['report_duty'] = array('in', $duty_list);
					$map['step'] = array('gt', 10);
				} else {
					$this -> error("没有权限");
				}
				break;
		}
	}

	function folder($fid) {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$emp_no = get_emp_no();
		$user_id = get_user_id();

		$flow_type_where['is_del'] = array('eq', 0);

		$flow_type_list = M("FlowType") -> where($flow_type_where) -> getField("id,name");
		$this -> assign("flow_type_list", $flow_type_list);

		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}
		$folder = $fid;
		$this -> assign("folder", $folder);

		$this -> _flow_auth_filter($folder, $map);
		$model = D("FlowView");

		if (I('mode') == 'export') {
			$this -> _folder_export($model, $map);
		} else {
			$this -> _list($model, $map, 'id desc');
		}
		$this -> display();
	}

	private function _folder_export($model, $map) {
		$list = $model -> where($map) -> select();
		$r = $model -> where($map) -> count();
		$model_flow_field = D("UdfField");
		if ($r <= 1000) {
			//导入thinkphp第三方类库
			Vendor('Excel.PHPExcel');

			//$inputFileName = "Public/templete/contact.xlsx";
			//$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
			$objPHPExcel = new \PHPExcel();

			$objPHPExcel -> getProperties() -> setCreator("小微OA") -> setLastModifiedBy("小微OA") -> setTitle("Office 2007 XLSX Test Document") -> setSubject("Office 2007 XLSX Test Document") -> setDescription("Test document for Office 2007 XLSX, generated using PHP classes.") -> setKeywords("office 2007 openxml php") -> setCategory("Test result file");
			// Add some data
			$i = 1;
			//dump($list);

			//编号，类型，标题，登录时间，部门，登录人，状态，审批，协商，传阅，审批情况，自定义字段
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", "编号") -> setCellValue("B$i", "类型") -> setCellValue("C$i", "标题") -> setCellValue("D$i", "登录时间") -> setCellValue("E$i", "部门") -> setCellValue("F$i", "登录人") -> setCellValue("G$i", "状态") -> setCellValue("H$i", "审批") -> setCellValue("I$i", "协商") -> setCellValue("J$i", "传阅") -> setCellValue("K$i", "审批情况");

			foreach ($list as $val) {
				$i++;
				//dump($val);
				$id = $val['id'];
				$doc_no = $val["doc_no"];
				//编号
				$name = $val["name"];
				//标题
				$confirm_name = strip_tags($val["confirm_name"]);
				//审批
				$consult_name = strip_tags($val["consult_name"]);
				//协商
				$refer_name = strip_tags($val["refer_name"]);
				//协商
				$type_name = $val["type_name"];
				//流程类型
				$user_name = $val["user_name"];
				//登记人
				$dept_name = $val["dept_name"];
				//不美分
				$create_time = $val["create_time"];
				
				$create_time = to_date($val["create_time"], 'Y-m-d H:i:s');
				//创建时间
				$step = show_step($val["step"]);

				//编号，类型，标题，登录时间，部门，登录人，状态，审批，协商，传阅，审批情况，自定义字段
				$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$i", $doc_no) -> setCellValue("B$i", $type_name) -> setCellValue("C$i", $name) -> setCellValue("D$i", $create_time) -> setCellValue("E$i", $dept_name) -> setCellValue("F$i", $user_name) -> setCellValue("G$i", $step) -> setCellValue("H$i", $confirm_name) -> setCellValue("I$i", $consult_name) -> setCellValue("J$i", $refer_name);
				$result = M("flow_log") -> where(array('flow_id' => $id)) -> select();
				$field_data = '';
				if (!empty($result)) {
					foreach ($result as $field) {
						$field_data = $field_data . $field['user_name'] . ":" . $field['comment'] . "\n";
					}
					$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("K$i", $field_data);
				}

				$field_list = $model_flow_field -> get_data_list($val["udf_data"]);
				//	dump($field_list);
				$k = 'K';
				if (!empty($field_list)) {
					foreach ($field_list as $field) {
						$k++;
						$field_data = $field['name'] . ":" . $field['val'];
						// $location = get_cell_location("J", $i, $k);
						$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("$k$i", $field_data);
					}
				}
			}
			// Rename worksheet
			$objPHPExcel -> getActiveSheet() -> setTitle('审批统计');

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel -> setActiveSheetIndex(0);
			$file_name = "审批统计.xlsx";
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
			header('Content-Disposition: attachment;filename="审批统计.csv"');
			header('Cache-Control: max-age=0');

			$fp = fopen('php://output', 'a');
			$title = array('编号', '类型', '标题', '登录时间', '部门', '登录人', '状态', '审批', '协商', '传阅', '审批情况', '自定义字段');
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
				//dump($val);
				$id = $val['id'];
				$doc_no = $val["doc_no"];
				//编号
				$name = $val["name"];
				//标题
				$confirm_name = strip_tags($val["confirm_name"]);
				//审批
				$consult_name = strip_tags($val["consult_name"]);
				//协商
				$refer_name = strip_tags($val["refer_name"]);
				//协商
				$type_name = $val["type_name"];
				//流程类型
				$user_name = $val["user_name"];
				//登记人
				$dept_name = $val["dept_name"];
				//不美分

				$create_time = to_date($val["create_time"], 'Y-m-d H:i:s');
				//创建时间
				$step = show_step_type($val["step"]);

				$result_list = M("flow_log") -> where(array('flow_id' => $id)) -> select();
				$field_data = '';
				$result = '';
				if (!empty($result_list)) {
					foreach ($result_list as $field) {

						$field_data = $field_data . $field['user_name'] . ":" . $field['comment'] . "\n";
					}
					$result = $field_data;

				}
				$r1 = array($doc_no, $type_name, $name, $create_time, $dept_name, $user_name, $step, $confirm_name, $consult_name, $refer_name, $result);

				$field_list = $model_flow_field -> get_data_list($val["udf_data"]);

				$t = 0;
				$r2 = array();
				if (!empty($field_list)) {

					foreach ($field_list as $field) {
						$r2[$t++] = $field['name'] . ":" . $field['val'];
					}

				}
				$row = array_merge($r1, $r2);
				// dump($row);
				foreach ($row as $i => $v) {
					// CSV的Excel支持GBK编码，一定要转换，否则乱码
					$row[$i] = iconv('utf-8', 'gbk', $v);
				}
				fputcsv($fp, $row);
			}
			fclose($fp);
			exit ;
		}
	}

	function add() {

		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$type_id = I('type');
		$model = M("FlowType");
		$flow_type = $model -> find($type_id);
		$this -> assign("flow_type", $flow_type);

		$model_flow_field = D("UdfField");
		$field_list = $model_flow_field -> get_field_list($type_id);
		$this -> assign("field_list", $field_list);
		$this -> display();
	}

	function read($id) {
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);
		
		$fid = I("fid");
		$this -> _flow_auth_filter($fid, $map);
			
		$model = D("Flow");
		$where['id'] = array('eq', $id);		
		$where['_logic'] = 'and';			
		$map['_complex'] = $where;			
				
		$vo = $model -> where($map) -> find();
		if (empty($vo)) {
			$this -> error("系统错误");
		}
	 
		$this -> assign("emp_no", $vo['emp_no']);
		$this -> assign("user_name", $vo['user_name']);
		$this -> assign('vo', $vo);

		$field_list = D("UdfField") -> get_data_list($vo['udf_data']);
		//dump($field_list);		
		$this -> assign("field_list", $field_list);
		
		$flow_type_id = $vo['type'];
		$model = M("FlowType");
		$flow_type = $model -> find($flow_type_id);
		$this -> assign("flow_type", $flow_type);
		
		
		//审批日志
		$model = M("FlowLog");
		$where = array();
		$where['flow_id'] = $id;
		$where['step'] = array('lt', 100);
		$where['_string'] = "result is not null";
		$flow_log = $model -> where($where) -> order("id") -> select();
		$this -> assign("flow_log", $flow_log);

		//参阅日志
		$model = M("FlowLog");
		$where = array();
		$where['flow_id'] = $id;
		$where['step'] = array('eq', 100);	
		$model->where($where)->setField('is_read',1);	
		$refer_flow_log = $model -> where($where) -> order("id") -> select();
		$this -> assign("refer_flow_log", $refer_flow_log);
		
		//当前审批信息
		$where = array();
		$where['flow_id'] = $id;
		$where['emp_no'] = get_emp_no();
		$where['_string'] = "result is null";
		$to_confirm = $model -> where($where) -> find();
		$this -> assign("to_confirm", $to_confirm);

		$where = array();
		$where['flow_id'] = $id;
		$where['emp_no'] = get_emp_no();
		$where['step'] = array('eq', 100);		
		$to_refer = $model -> where($where) -> find();
		$is_read=$model->where($where)->setField('is_read',1);
		$this -> assign("to_refer", $to_refer);

		if (!empty($to_confirm)) {
			$is_edit = $flow_type['is_edit'];
			$this -> assign("is_edit", $is_edit);
		} else {
			$is_edit = $flow_type['is_edit'];
			$this -> assign("is_edit", 0);
		}

		$where = array();
		$where['flow_id'] = $id;
		$where['_string'] = "result is not null";
		$where['emp_no'] = array('neq', $vo['emp_no']);
		$confirmed = $model -> Distinct(true) -> where($where) -> field('emp_no,user_name') -> select();
		$this -> assign("confirmed", $confirmed);
		$this -> display();
	}

	function edit($id) {
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$folder = I('fid');
		$this -> assign("folder", $folder);

		if (empty($folder)) {
			$this -> error("系统错误");
		}
		$this -> _flow_auth_filter($folder, $map);

		$model = D("Flow");
		$where['id'] = array('eq', $id);
		$where['_logic'] = 'and';
		$map['_complex'] = $where;
		$vo = $model -> where($map) -> find();
		if (empty($vo)) {
			$this -> error("系统错误");
		}
		$this -> assign('vo', $vo);

		$field_list = D("UdfField") -> get_data_list($vo['udf_data']);
		//dump($field_list);
		$this -> assign("field_list", $field_list);

		$model = M("FlowType");
		$type = $vo['type'];
		$flow_type = $model -> find($type);
		$this -> assign("flow_type", $flow_type);

		$model = M("FlowLog");
		$where['flow_id'] = $id;
		$where['_string'] = "result is not null";
		$flow_log = $model -> where($where) -> select();

		$this -> assign("flow_log", $flow_log);
		$where = array();
		$where['flow_id'] = $id;
		$where['emp_no'] = get_emp_no();
		$where['_string'] = "result is null";
		$confirm = $model -> where($where) -> select();

		$this -> assign("confirm", $confirm[0]);
		$this -> display();
	}

	function del($id){
		$this->_del($id);
	}
	
	/** 插入新新数据  **/
	protected function _insert($name = CONTROLLER_NAME) {

		$model = D($name);
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model->confirm_name=I('confirm_name');
		$str_confirm = D("Flow") -> _conv_auditor($model -> confirm);
		$str_consult = D("Flow") -> _conv_auditor($model -> consult);
		$str_auditor = $str_confirm . $str_consult;
		if (empty($str_auditor)) {
			$this -> error('没有找到任何审核人');
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
		$flow_id = $model -> id;
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

	public function approve() {
		$model = D("FlowLog");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> result = 1;

		$flow_id = $model -> flow_id;
		$step = $model -> step;
		//保存当前数据对象
		$list = $model -> save();

		//保存当前数据对象
		$model = D("FlowLog");
		$where['step'] = array('eq', $step);
		$where['flow_id'] = array('eq', $flow_id);
		$where['_string'] = 'result is null';
		$model -> where($where) -> setField('is_del', 1);

		if ($list !== false) {//保存成功
			D("Flow") -> next_step($flow_id, $step);
			$this -> assign('jumpUrl', U('flow/folder', 'fid=confirm'));
			$this -> success('操作成功!');
		} else {
			//失败提示
			$this -> error('操作失败!');
		}
	}

	public function reject() {
		$model = D("FlowLog");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> result = 0;
		if (in_array('user_id', $model -> getDbFields())) {
			$model -> user_id = get_user_id();
		};
		if (in_array('user_name', $model -> getDbFields())) {
			$model -> user_name = get_user_name();
		};

		$flow_id = $model -> flow_id;
		$step = $model -> step;
		$list = $model -> save();

		//可以裁决的人有多个人的时候，一个人评价完以后，禁止其他人重复裁决。
		$model = D("FlowLog");
		$where['step'] = array('eq', $step);
		$where['flow_id'] = array('eq', $flow_id);
		$where['_string'] = 'result is null';
		$model -> where($where) -> setField('is_del', 1);

		if ($list !== false) {//保存成功

			M("Flow") -> where("id=$flow_id") -> setField('step', 0);
			$flow = M("Flow") -> find($flow_id);

			$push_data['type'] = '审批';
			$push_data['action'] = '被否决';
			$push_data['title'] = $flow['name'];
			$push_data['content'] = '审核人：' . get_dept_name()."-".get_user_name();
			$push_data['url']=U('Flow/read',"id={$flow['id']}&fid=submit&return_url=Flow/index");
			send_push($push_data, $flow['user_id']);

			$this -> assign('jumpUrl', U('flow/folder', 'fid=confirm'));
			$this -> success('操作成功!');
		} else {
			//失败提示
			$this -> error('操作失败!');
		}
	}

	function back_to($emp_no) {
		$model = D("FlowLog");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}

		$model -> result = 2;
		if (in_array('user_id', $model -> getDbFields())) {
			$model -> user_id = get_user_id();
		};
		if (in_array('user_name', $model -> getDbFields())) {
			$model -> user_name = get_user_name();
		};

		$flow_id = $model -> flow_id;
		$step = $model -> step;
		//保存当前数据对象
		$list = $model -> save();
		if ($list !== false) {//保存成功
			D("Flow") -> back_to($flow_id, $emp_no);
			$this -> assign('jumpUrl', U('flow/folder?fid=confirm'));
			$this -> success('操作成功!');
		} else {
			//失败提示
			$this -> error('操作失败!');
		}
		return;
	}

	public function refer() {
		$model = D("FlowLog");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		//保存当前数据对象
		$list = $model -> save();
		
		if ($list !== false) {//保存成功		
			$this -> assign('jumpUrl', U('flow/folder', 'fid=receive_unread'));	
			$this -> success('操作成功!');
		} else {
			//失败提示
			$this -> error('操作失败!');
		}
	}

	function send_refer($flow_id, $emp_list) {
		$emp_list = array_filter(explode(",", $emp_list));
		$model = D("Flow") -> send_to_refer($flow_id, $emp_list);
		$this -> success('发送成功');
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}

	public function upload() {
		$this -> _upload();
	}

	protected function _assign_tag_list() {
		$model = D("SystemTag");
		$tag_list = $model -> get_tag_list('id,name', 'FlowType');
		$this -> assign("tag_list", $tag_list);
	}

	public function field_manage($row_type) {
		$this -> assign("folder_name", "自定义字段管理");
		$this -> _field_manage($row_type);
	}
}
