<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

function think_encrypt($data, $key = '', $expire = 0) {
	$key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = base64_encode($data);
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l)
			$x = 0;
		$char .= substr($key, $x, 1);
		$x++;
	}

	$str = sprintf('%010d', $expire ? $expire + time() : 0);

	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
	}
	return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

function think_decrypt($data, $key = '') {
	$key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = str_replace(array('-', '_'), array('+', '/'), $data);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	$data = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data = substr($data, 10);

	if ($expire > 0 && $expire < time()) {
		return '';
	}
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = $str = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l)
			$x = 0;
		$char .= substr($key, $x, 1);
		$x++;
	}

	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		} else {
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}

function is_weixin() {
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		return true;
	}
	return false;
}

function badge_count_work_order() {
	return badge_count_todo_work_order() + badge_count_doing_work_order();
}

//等我接受的任务
function badge_count_todo_work_order() {
	$work_order_list_todo_count = 0;
	$where = array();
	$where_log['type'] = 1;
	$where_log['status'] = 0;
	$where_log['executor'] = get_user_id();
	$work_order_list = M("WorkOrderLog") -> where($where_log) -> getField('task_id', true);
	if (!empty($work_order_list)) {
		$where['id'] = array('in', $work_order_list);
		$where['is_del'] = 0;
		$work_order_list_todo_count = M("WorkOrder") -> where($where) -> count();
	}
	return $work_order_list_todo_count;
}

function badge_count_doing_work_order() {
	//等我接受的任务
	$work_order_list_doing_count = 0;
	$where = array();
	$where_log['type'] = 1;
	$where_log['status'] = array('in', '1,2');
	$where_log['executor'] = get_user_id();
	$work_order_list = M("WorkOrderLog") -> where($where_log) -> getField('task_id', true);
	if (!empty($work_order_list)) {
		$where['id'] = array('in', $work_order_list);
		$where['is_del'] = 0;
		$work_order_list_doing_count = M("WorkOrder") -> where($where) -> count();
	}
	return $work_order_list_doing_count;
}

function badge_count_task() {
	return badge_count_no_finish_task() + badge_count_dept_task() + badge_count_no_assign_task();
}

function badge_count_no_finish_task() {
	//等我接受的任务
	$where = array();
	$where_log['type'] = 1;
	$where_log['status'] = array('lt', 20);
	$where_log['executor'] = get_user_id();
	$task_list = M("TaskLog") -> where($where_log) -> getField('task_id', true);
	$task_todo_count = 0;
	if (!empty($task_list)) {
		$where['id'] = array('in', $task_list);
		$where['is_del'] = array('eq', 0);
		$task_todo_count = M("Task") -> where($where) -> count();
	}
	return $task_todo_count;
}

function badge_count_no_assign_task() {
	//等我接受的任务
	$prefix = C('DB_PREFIX');

	$assign_list = M("Task") -> getField('id', true);

	$sql = "select id from {$prefix}task task where status=0 and not exists (select * from {$prefix}task_log task_log where task.id=task_log.task_id)";
	$task_list = M() -> query($sql);

	if (empty($task_list)) {
		return 0;
	} else {
		foreach ($task_list as $key => $val) {
			$list[] = $val['id'];
		}
		$where['id'] = array('in', $list);
		$where['is_del'] = array('eq', 0);
		$task_no_assign_count = M("Task") -> where($where) -> count();
		return $task_no_assign_count;
	}
}

function badge_count_dept_task() {

	//我部门任务
	$where = array();
	$auth = D("Role") -> get_auth("Task");
	if ($auth['admin']) {
		$where_log['type'] = 2;
		$where_log['executor'] = get_dept_id();
		$where_log['status'] = array('eq', '0');
		$task_list = M("TaskLog") -> where($where_log) -> getField('task_id', TRUE);
		if (!empty($task_list)) {
			$where['id'] = array('in', $task_list);
			$where['is_del'] = array('eq', 0);
		} else {
			return 0;
		}
	} else {
		return 0;
	}

	$task_dept_count = M("Task") -> where($where) -> count();
	return $task_dept_count;
}

function badge_count_mail_inbox() {
	$user_id = get_user_id();
	$where['user_id'] = $user_id;
	$where['is_del'] = array('eq', '0');
	$where['folder'] = array('eq', 1);
	$where['read'] = array('eq', '0');
	$new_mail_inbox = M("Mail") -> where($where) -> count();
	return $new_mail_inbox;
}

function badge_count_mail_user_folder() {
	//获取未读邮件
	$user_id = get_user_id();
	$where['user_id'] = $user_id;
	$where['is_del'] = array('eq', '0');
	$where['folder'] = array('gt', 6);
	$where['read'] = array('eq', '0');
	$new_mail_myfolder = M("Mail") -> where($where) -> count();
	return $new_mail_myfolder;
}

function badge_count_flow_todo() {
	//获取待裁决
	$where = array();
	$FlowLog = M("FlowLog");

	$emp_no = get_emp_no();
	$where['emp_no'] = $emp_no;
	$where['is_del'] = array('eq', 0);
	$where['_string'] = "result is null";
	$log_list = $FlowLog -> where($where) -> field('flow_id') -> select();
	$log_list = rotate($log_list);

	$new_confirm_count = 0;
	if (!empty($log_list)) {
		$map['id'] = array('in', $log_list['flow_id']);
		$new_confirm_count = M("Flow") -> where($map) -> count();
	}
	return $new_confirm_count;
}

function badge_count_gov_doc() {
	$model = M('gov_doc_auth');
	$auth = $model -> select();
	$where['user_id'] = get_user_id();
	$list = $model -> where($where) -> find();
	$user_auth = $list['set_auth'];
	if ($user_auth == 0) {
		$model = D('GovDoc');
		$where['user_id'] = get_user_id();
		$where['is_del'] = 0;
		$where['is_read'] = 0;
		$gov_doc_count = $model -> where($where) -> count();
		return $gov_doc_count;

	}

	$model = D('GovDocView');
	$where['user_id'] = get_user_id();
	$where['is_del'] = 0;
	$where['state'] = 0;
	$gov_doc_count = $model -> where($where) -> count();
	return $gov_doc_count;
}

function badge_count_flow_receive() {
	//获取收到的流程
	$emp_no = get_emp_no();
	$where['emp_no'] = $emp_no;
	$where['step'] = 100;
	$where['_string'] = "comment is null";

	$log_list = M("FlowLog") -> where($where) -> field('flow_id') -> select();
	$log_list = rotate($log_list);
	$new_receive_count = 0;
	if (!empty($log_list)) {
		$map['id'] = array('in', $log_list['flow_id']);
		$new_receive_count = M("Flow") -> where($map) -> count();
	}
	return $new_receive_count;
}

function badge_count_todo() {
	//获取待办事项
	$where = array();
	$user_id = get_user_id();
	$where['user_id'] = $user_id;
	$where['status'] = array("in", "1,2");
	$new_todo_count = M("Todo") -> where($where) -> count();
	return $new_todo_count;
}

function badge_count_schedule() {
	$where['user_id'] = get_user_id();
	$where['is_del'] = array('eq', 0);
	$where['start_time'] = array("egt", date("Y-m-d"));
	//$where['end_time'] = array("egt", date("Y-m-d"));
	$new_schedule_count = M("Schedule") -> where($where) -> count();
	return $new_schedule_count;
}

function badge_count_message() {
	//获取最新消息
	$model = M("Message");
	$where = array();
	$user_id = get_user_id();
	$where['owner_id'] = $user_id;
	$where['receiver_id'] = $user_id;
	$where['is_read'] = array('eq', '0');
	$new_message_count = M("Message") -> where($where) -> count();
	return $new_message_count;
}

function badge_count_info($id) {

	$model = D("InfoView");
	$map['is_del'] = array('eq', '0');
	$map['folder'] = array('eq', $id);
	$map['create_time'] = array("egt", time() - 3600 * 24 * 30);

	$user_id = get_user_id();
	$where_scope['user_id'] = array('eq', $user_id);
	$scope_list = M("InfoScope") -> where($where_scope) -> getField('info_id', TRUE);

	if (!empty($scope_list)) {
		$map['id'] = array('in', $scope_list);
	} else {
		$map['_string'] = " 1=2";
	}

	$model = D("InfoView");
	$info_list = $model -> where($map) -> getField('id', true);

	$readed_info = M("UserConfig") -> where("id=$user_id") -> getField('readed_info');
	$readed_info = array_filter(explode(',', $readed_info));

	if (!empty($info_list)) {
		$un_read_doc = array_diff($info_list, $readed_info);
	} else {
		$un_read_doc = array();
	}
	return count($un_read_doc);
}

function badge_count_system_folder($id) {
	//获取最新消息
	$where['id'] = array('eq', $id);
	$controller = M("SystemFolder") -> where($where) -> getField('controller');
	$count = 0;
	switch ($controller) {
		case 'Info' :
			$count = badge_count_info($id);
			break;
		default :
			break;
	}
	return $count;
}

function is_mobile($mobile) {
	return preg_match("/^(?:13\d|14\d|15\d|18[0123456789])-?\d{5}(\d{3}|\*{3})$/", $mobile);
}

function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * 发送HTTP请求方法，目前只支持CURL发送请求
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
function http($url, $params, $method = 'GET', $header = array(), $multi = false) {
	$opts = array(CURLOPT_TIMEOUT => 30, CURLOPT_RETURNTRANSFER => 1, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_HTTPHEADER => $header);

	/* 根据请求类型设置特定参数 */
	switch(strtoupper($method)) {
		case 'GET' :
			$opts[CURLOPT_URL] = $url . '?' . str_replace("&amp;", "&", http_build_query($params));
			break;
		case 'POST' :
			//判断是否传输文件
			//$params = $multi ? $params : http_build_query($params);
			$opts[CURLOPT_URL] = $url;
			$opts[CURLOPT_POST] = 1;
			$opts[CURLOPT_POSTFIELDS] = $params;
			break;
		default :
			throw new Exception('不支持的请求方式！');
	}

	/* 初始化并执行curl请求 */
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$error = curl_error($ch);
	curl_close($ch);
	if ($error)
		throw new Exception('请求发生错误：' . $error);
	return $data;
}

function upload_filter($val) {
	$allow_ext = explode(",", C('UPLOAD_FILE_EXT'));
	if (in_array($val, $allow_ext)) {
		return true;
	} else {
		return false;
	}
}

function get_img_info($img) {
	$img_info = getimagesize($img);
	if ($img_info !== false) {
		$img_type = strtolower(substr(image_type_to_extension($img_info[2]), 1));
		$info = array("width" => $img_info[0], "height" => $img_info[1], "type" => $img_type, "mime" => $img_info['mime'], );
		return $info;
	} else {
		return false;
	}
}

function get_return_url($level = null) {
	if (empty($level)) {
		$return_url = cookie('return_url');
	} else {
		$return_url = cookie('return_url_' . $level);
	}
	return $return_url;
}

function get_system_config($code) {
	$model = M("SystemConfig");
	$where['code'] = array('eq', $code);
	$count = $model -> where($where) -> count();
	if ($count > 1) {
		return $model -> where($where) -> getfield("val,name");
	} else {
		return $model -> where($where) -> getfield("val");
	}
}

function get_user_config($field) {
	$model = M("UserConfig");
	$user_id = get_user_id();
	$vo = $model -> find($user_id);
	if (empty($vo)) {
		$model -> add(array('id' => $user_id));
	}
	$where['id'] = array('eq', $user_id);
	$result = $model -> where($where) -> getfield($field);
	if (empty($result)) {
		return get_system_config($field);
	} else {
		return $result;
	}
}

function get_user_info($id, $field) {
	$model = D("UserView");
	$where['id'] = array('eq', $id);
	$result = $model -> where($where) -> getfield($field);
	//dump($field);
	return $result;
}

function get_user_id($emp_no = null) {
	$user_id = session(C('USER_AUTH_KEY'));
	return isset($user_id) ? $user_id : 0;
}

function get_emp_no($user_id = null) {
	if (empty($user_id)) {
		$emp_no = session("emp_no");
		return isset($emp_no) ? $emp_no : 0;
	} else {
		$where['id'] = array('eq', $user_id);
		return M("User") -> where($where) -> getField('emp_no');
	}
}

function get_user_name($user_id = null) {
	if (empty($user_id)) {
		$user_name = session('user_name');
		return isset($user_name) ? $user_name : 0;
	} else {
		$where['id'] = array('eq', $user_id);
		return M("User") -> where($where) -> getField('name');
	}
}

function get_dept_id($user_id = null) {
	if (empty($user_id)) {
		return session('dept_id');
	} else {
		$where['id'] = array('eq', $user_id);
		return M("User") -> where($where) -> getField('dept_id');
	}
}

function get_dept_name($dept_id = null) {
	if (empty($user_id)) {
		$result = M("Dept") -> find(session("dept_id"));
		return $result['name'];
	} else {
		$where['id'] = array('eq', $dept_id);
		return M("Dept") -> where($where) -> getField('name');
	}
}

function get_controller($str) {
	$arr_str = explode("/", $str);
	return $arr_str[0];
}

function to_date($time, $format = 'Y-m-d H:i:s') {
	if (empty($time)) {
		return '';
	}
	$format = str_replace('#', ':', $format);
	return date($format, $time);
}

function date_to_int($date) {
	$date = explode("-", $date);
	$time = explode(":", "00:00");
	$time = mktime($time[0], $time[1], 0, $date[1], $date[2], $date[0]);
	return $time;
}

function get_offset_date($date, $i, $type = "d") {
	$date = explode("-", $date);
	switch ($type) {
		case 'y' :
			$time = mktime(0, 0, 0, $date[1], $date[2], $date[0] + $i);
			break;
		case 'm' :
			$time = mktime(0, 0, 0, $date[1] + $i, $date[2], $date[0]);
			break;
		case 'd' :
			$time = mktime(0, 0, 0, $date[1], $date[2] + $i, $date[0]);
			break;
		default :
			break;
	}
	return date('Y-m-d', $time);
}

function fix_time($time) {
	return substr($time, 0, 5);
}

function filter_search_field($v1) {
	if ($v1 == "keyword")
		return true;
	$prefix = substr($v1, 0, 3);
	$arr_key = array("be_", "en_", "eq_", "li_", "lt_", "gt_", "bt_");
	if (in_array($prefix, $arr_key)) {
		return true;
	} else {
		return false;
	}
}

function filter_udf_field($val) {
	if (strpos($val, "udf_field") !== false) {
		return true;
	} else {
		return false;
	}
}

function get_cell_location($col, $row, $col_offset = 0, $row_offset = 0) {
	if (!is_numeric($col)) {
		$col = ord($col) - 65;
	}
	$location = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
	$col = $col + $col_offset;
	$row = $row + $row_offset;
	return $location[$col] . $row;
}

function get_model_fields($model) {
	$arr_field = array();
	if (isset($model -> viewFields)) {
		foreach ($model->viewFields as $key => $val) {
			unset($val['_on']);
			unset($val['_type']);
			if (!empty($val[0]) && ($val[0] == "*")) {
				$model = M($key);
				$fields = $model -> getDbFields();
				$arr_field = array_merge($arr_field, array_values($fields));
			} else {
				$arr_field = array_merge($arr_field, array_values($val));
			}
		}
	} else {
		$arr_field = $model -> getDbFields();
	}
	return $arr_field;
}

function show_udf_field($udf_field) {
	$html = "";
	if (!empty($udf_field)) {
		foreach ($udf_field as $key => $val) {
			list($show, $class) = explode("|", $val['config']);
			$htmi = array();
			$html .= '<span class="' . $class . '">' . $val['name'] . '</span>';
		}
	}
	return $html;
}

function show_udf_val($udf_field, $udf_data) {
	$field_data = json_decode($udf_data, true);
	$html = "";
	if (!empty($udf_field)) {
		foreach ($udf_field as $key => $val) {
			list($show, $class) = explode("|", $val['config']);
			$html .= '<span class="' . $class . ' autocut" title="' . $field_data[$val['id']] . '">' . $field_data[$val['id']] . '&nbsp;</span>';
		}
	}
	return $html;
}

function show_step_type($step) {
	if ($step >= 20 && $step < 30) {
		return "审批";
	}
	if ($step >= 30) {
		return "协商";
	}
}

function show_result($result) {
	if ($result == 1) {
		return "同意";
	}
	if ($result == 0) {
		return "否决";
	}
	if ($result == 2) {
		return "退回";
	}
}

function show_step($step) {
	if ($step == 40) {
		return "通过";
	}
	if ($step > 30) {
		return "协商中";
	}
	if ($step == 30) {
		return "待协商";
	}
	if ($step > 20) {
		return "审批中";
	}
	if ($step == 20) {
		return "待审批";
	}
	if ($step == 10) {
		return "临时保管";
	}
	if ($step == 0) {
		return "否决";
	}
}

function IP($ip = '', $file = 'UTFWry.dat') {
	$_ip = array();
	if (isset($_ip[$ip])) {
		return $_ip[$ip];
	} else {
		import("ORG.Net.IpLocation");
		$iplocation = new IpLocation($file);
		$location = $iplocation -> getlocation($ip);
		$_ip[$ip] = $location['country'] . $location['area'];
	}
	return $_ip[$ip];
}

function sort_by($array, $keyname = null, $sortby = 'asc') {
	//dump($array);
	$myarray = $inarray = array();
	# First store the keyvalues in a seperate array
	foreach ($array as $i => $befree) {
		$myarray[$i] = $array[$i][$keyname];
	}
	//dump($array);
	# Sort the new array by
	switch ($sortby) {
		case 'asc' :
			# Sort an array and maintain index association...
			asort($myarray, SORT_STRING);
			break;
		case 'desc' :
		case 'arsort' :
			# Sort an array in reverse order and maintain index association
			arsort($myarray, SORT_STRING);
			break;
		case 'natcasesor' :
			# Sort an array using a case insensitive "natural order" algorithm
			natcasesort($myarray);
			break;
	}
	# Rebuild the old array
	foreach ($myarray as $key => $befree) {
		$inarray[] = $array[$key];
	}
	return $inarray;
}

function fix_array_key($list, $key) {
	$arr = null;
	foreach ($list as $val) {
		$arr[$val[$key]] = $val;
	}
	return $arr;
}

function fill_option($list, $data = null) {
	$html = "";
	if (is_array($list)) {
		foreach ($list as $key => $val) {

			if (is_array($val)) {
				$id = $val['id'];
				$name = $val['name'];
				if (empty($data)) {
					$selected = "";
				} else {
					$selected = "selected";
				}
				$html = $html . "<option value='{$id}' $selected>{$name}</option>";
			} else {
				if ($key == $data) {
					$selected = "selected";
				} else {
					$selected = "";
				}
				$html = $html . "<option value='{$key}' $selected>{$val}</option>";
			}
		}
	}

	echo $html;
}

/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat('0123456789', 3);
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) {//位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
	}
	if ($type != 4) {
		$chars = str_shuffle($chars);
		$str = substr($chars, 0, $len);
	} else {
		// 中文随机字
		for ($i = 0; $i < $len; $i++) {
			$str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
		}
	}
	return $str;
}

function list_to_tree($list, $root = 0, $pk = 'id', $pid = 'pid', $child = '_child') {
	// 创建Tree
	$tree = array();
	if (is_array($list)) {
		// 创建基于主键的数组引用

		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] = &$list[$key];
		}

		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = 0;
			if (isset($data[$pid])) {
				$parentId = $data[$pid];
			}
			if ((string)$root == $parentId) {
				$tree[] = &$list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent = &$refer[$parentId];
					$parent[$child][] = &$list[$key];
				}
			}
		}
	}
	return $tree;
}

function tree_to_list($tree, $level = 0, $pk = 'id', $pid = 'pid', $child = '_child') {
	$list = array();
	if (is_array($tree)) {
		foreach ($tree as $val) {
			$val['level'] = $level;
			if (isset($val['_child'])) {
				$child = $val['_child'];
				if (is_array($child)) {
					unset($val['_child']);
					$list[] = $val;
					$list = array_merge($list, tree_to_list($child, $level + 1));
				}
			} else {
				$list[] = $val;
			}
		}
		return $list;
	}
}

function left_menu($tree, $level = 0) {
	$level++;
	$html = "";
	if (is_array($tree)) {
		$html = "<ul class=\"tree_menu\">\r\n";
		foreach ($tree as $val) {
			if (isset($val["name"])) {
				$title = $val["name"];
				if (!empty($val["url"])) {
					$url = U($val['url']);
				} else {
					$url = "#";
				}
				$id = $val["id"];
				if (empty($val["id"])) {
					$id = $val["name"];
				}
				if (isset($val['_child'])) {
					$html = $html . "<li>\r\n<a node=\"$id\" href=\"" . "$url\"><i class=\"fa fa-angle-right level$level\"></i><span>$title</span></a>\r\n";
					$html = $html . left_menu($val['_child'], $level);
					$html = $html . "</li>\r\n";
				} else {
					$html = $html . "<li>\r\n<a  node=\"$id\" href=\"" . "$url\"><i class=\"fa fa-angle-right level$level\"></i><span>$title</span></a>\r\n</li>\r\n";
				}
			}
		}
		$html = $html . "</ul>\r\n";
	}
	return $html;
}

function select_tree_menu($tree) {
	$html = "";
	if (is_array($tree)) {
		$list = tree_to_list($tree);
		foreach ($list as $val) {
			$html = $html . "<option value='{$val['id']}'>" . str_pad("", $val['level'] * 3, "│") . "├─" . "{$val['name']}</option>";
		}
	}
	return $html;
}

function popup_tree_menu($tree, $level = 0) {
	$level++;
	$html = "";
	if (is_array($tree)) {
		$html = "<ul class=\"tree_menu\">\r\n";
		foreach ($tree as $val) {
			if (isset($val["name"])) {
				$title = $val["name"];
				$id = $val["id"];
				if (empty($val["id"])) {
					$id = $val["name"];
				}
				if (!empty($val["is_del"])) {
					$del_class = "is_del";
				} else {
					$del_class = "";
				}
				if (isset($val['_child'])) {
					$html = $html . "<li>\r\n<a class=\"$del_class\" node=\"$id\" ><i class=\"fa fa-angle-right level$level\"></i><span>$title</span></a>\r\n";
					$html = $html . popup_tree_menu($val['_child'], $level);
					$html = $html . "</li>\r\n";
				} else {
					$html = $html . "<li>\r\n<a class=\"$del_class\" node=\"$id\" ><i class=\"fa fa-angle-right level$level\"></i><span>$title</span></a>\r\n</li>\r\n";
				}
			}
		}
		$html = $html . "</ul>\r\n";
	}
	return $html;
}

function popup_tree_menu2($tree, $level = 0) {
	$level++;
	$html = "";
	if (is_array($tree)) {
		$html = "<ul class=\"tree_menu\">\r\n";
		foreach ($tree as $val) {
			if (isset($val["name"])) {
				$title = $val['name'];
				$id = $val["id"];
				$pid = $val['pid'];
				if (empty($val["id"])) {
					$id = $val["name"];
				}
				if (!empty($val["is_del"])) {
					$del_class = "is_del";
				} else {
					$del_class = "";
				}
				if (isset($val['_child'])) {
					$html = $html . "<li class=\"level_{$level} dept dept_{$id} dept_pid_{$pid}\" dept_pid_id=\"{$pid}\" dept_id=\"{$id}\">\r\n<a node=\"$id\" ><span><i class=\"fa fa-angle-right\"></i>$title</span></a><i class=\"check \"></i>\r\n";
					$html = $html . get_emp_list($val['id']);
					$html = $html . popup_tree_menu2($val['_child'], $level);
					$html = $html . "</li>\r\n";
				} else {
					$html = $html . "<li class=\"level_{$level} dept dept_{$id} dept_pid_{$pid}\" dept_pid_id=\"{$pid}\" dept_id=\"{$id}\">\r\n<a node=\"$id\" ><span><i class=\"fa fa-angle-right\"></i>$title</span></a><i class=\"check\"></i>\r\n</li>\r\n";
					$html = $html . get_emp_list($val['id']);
				}
			}
		}
		$html = $html . "</ul>\r\n";
	}
	return $html;
}

function get_emp_list($dept_id) {
	$where['is_del'] = array('eq', 0);
	$where['dept_id'] = array('eq', $dept_id);
	$user_list = M("User") -> where($where) -> select();
	$html = '';
	foreach ($user_list as $key => $val) {
		$id = $val['id'];
		$html = $html . "<li class=\"emp dept_pid_{$dept_id}\" user_id=\"$id\">\r\n<a><span><i class=\"fa fa-user\"></i>{$val['name']}</span><i class=\"check fa \"></i></a>\r\n</li>";
	}
	return $html;
}

function sub_tree_menu($tree, $level = 0) {
	$level++;
	$html = "";
	if (is_array($tree)) {
		$html = "<ul class=\"tree_menu\">\r\n";
		foreach ($tree as $val) {
			if (isset($val["name"])) {
				$title = $val["name"];
				$id = $val["id"];
				if (empty($val["id"])) {
					$id = $val["name"];
				}
				if (isset($val['_child'])) {
					$html = $html . "<li>\r\n<a node=\"$id\"><i class=\"fa fa-angle-right level$level\"></i><span>$title</span></a>\r\n";
					$html = $html . sub_tree_menu($val['_child'], $level);
					$html = $html . "</li>\r\n";
				} else {
					$html = $html . "<li>\r\n<a  node=\"$id\" ><i class=\"fa fa-angle-right level$level\"></i><span>$title</span></a>\r\n</li>\r\n";
				}
			}
		}
		$html = $html . "</ul>\r\n";
	}
	return $html;
}

function dropdown_menu($tree, $level = 0) {
	$level++;
	$html = "";
	if (is_array($tree)) {
		foreach ($tree as $val) {
			if (isset($val["name"])) {
				$title = $val["name"];
				$id = $val["id"];
				if (empty($val["id"])) {
					$id = $val["name"];
				}
				if (isset($val['_child'])) {
					$html = $html . "<li id=\"$id\" class=\"level$level\"><a>$title</a>\r\n";
					$html = $html . dropdown_menu($val['_child'], $level);
					$html = $html . "</li>\r\n";
				} else {
					$html = $html . "<li  id=\"$id\"  class=\"level$level\">\r\n<a>$title</a>\r\n</li>\r\n";
				}
			}
		}
	}
	return $html;
}

function u_str_pad($cnt, $str) {
	$tmp = '';
	for ($i = 1; $i <= $cnt; $i++) {
		$tmp = $tmp . $str;
	}
	return $tmp;
}

function show_contact($str, $mode = "show") {
	$tmp = '';

	if (!empty($str)) {
		$contacts = array_filter(explode(';', $str));
		if (count($contacts) > 1) {
			foreach ($contacts as $contact) {
				$arr = explode('|', $contact);
				$name = htmlspecialchars(rtrim($arr[0]));
				$data = htmlspecialchars(rtrim($arr[1]));
				if ($mode == "edit") {
					$tmp = $tmp . "<span data=\"$data\"><nobr><b  title=\"$name - $data\">$name</b><a class=\"del\" title=\"删除\"><i class=\"fa fa-times\"></i></a></nobr></span>";
				} else {
					$tmp = $tmp . "<a data=\"$data\" title=\"$name - $data\" >$name;</a>&nbsp;";
				}
			}
		} else {
			$arr = explode('|', $contacts[0]);
			$name = htmlspecialchars(rtrim($arr[0]));
			$data = htmlspecialchars(rtrim($arr[1]));
			$tmp = "";
			if ($mode == "edit") {
				$tmp = $tmp . "<span data=\"$data\"><nobr><b  title=\"$name - $data\">$name</b><a class=\"del\" title=\"删除\"><i class=\"fa fa-times\"></i></a></nobr></span>";
			} else {
				$tmp = $tmp . "<a title=\"$name\" >$name</a>";
			}
		}
	}
	return $tmp;
}

function show_recent($str) {
	$contacts = explode(';', $str);
	if (count($contacts) > 2) {
		foreach ($contacts as $contact) {
			if (strlen($contact) > 6) {
				$arr = explode('|', $contact);
				$name = rtrim($arr[0]);
				$email = rtrim($arr[1]);
				$tmp = $tmp . "<li><span title=\"$email\">$name</span></li>";
			}
		}
	} else {
		$arr = explode('|', $contacts[0]);
		$name = rtrim($arr[0]);
		$email = rtrim($arr[1]);
		$tmp = "";
		$tmp = $tmp . "<li><span title=\"$email\">$name</span></li>";
	}
	return $tmp;
}

function is_dept($val) {
	if (strpos($val, "dept@group") == false) {
		return false;
	} else {
		return true;
	}
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from, $to) {
	$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
	$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
	if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
		//如果编码相同或者非字符串标量则不转换
		return $fContents;
	}
	if (is_string($fContents)) {
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($fContents, $to, $from);
		} elseif (function_exists('iconv')) {
			return iconv($from, $to, $fContents);
		} else {
			return $fContents;
		}
	} elseif (is_array($fContents)) {
		foreach ($fContents as $key => $val) {
			$_key = auto_charset($key, $from, $to);
			$fContents[$_key] = auto_charset($val, $from, $to);
			if ($key != $_key)
				unset($fContents[$key]);
		}
		return $fContents;
	} else {
		return $fContents;
	}
}

function get_ext($filename) {
	$pathinfo = pathinfo($filename);
	return $pathinfo['extension'];
}

function show_refer($emp_list) {
	$arr_emp_no = array_filter(explode('|', $emp_list));
	if (count($arr_emp_no) > 1) {
		$model = D("UserView");
		foreach ($arr_emp_no as $emp_no) {
			$where['emp_no'] = array('eq', substr($emp_no, 4));
			$emp = $model -> where($where) -> find();
			$emp_no = $emp['emp_no'];
			$user_name = $emp['name'];
			$position_name = $emp['position_name'];
			$str .= "<span data=\"$emp_no\" id=\"$emp_no\"><nobr><b title=\"$user_name/$position_name\">$user_name/$position_name</b></nobr><b>;&nbsp;</b></span>";
		}
		return $str;
	} else {
		return "";
	}
}

function reunit($size) {
	$unit = " B";
	if ($size > 1024) {
		$size = $size / 1024;
		$unit = " KB";
	}
	if ($size > 1024) {
		$size = $size / 1024;
		$unit = " MB";
	}
	if ($size > 1024) {
		$size = $size / 1024;
		$unit = " GB";
	}
	return round($size, 2) . $unit;
}

function rotate($a) {
	$b = array();
	if (is_array($a)) {
		foreach ($a as $val) {
			foreach ($val as $k => $v) {
				$b[$k][] = $v;
			}
		}
	}
	return $b;
}

function utf_strlen($string) {
	return count(mb_str_split($string));
}

function utf_str_sub($string, $cnt) {
	$charlist = mb_str_split($string);
	$new = array_chunk($charlist, $cnt);
	return implode($new[0]);
}

function get_letter($string) {
	$charlist = mb_str_split($string);
	return implode(array_map("get_first_char", $charlist));
}

function mb_str_split($string) {
	// Split at all position not after the start: ^
	// and not before the end: $
	return preg_split('/(?<!^)(?!$)/u', $string);
}

function get_first_char($s0) {
	$fchar = ord(substr($s0, 0, 1));
	if (($fchar >= ord("a") and $fchar <= ord("z")) or ($fchar >= ord("A") and $fchar <= ord("Z")))
		return strtoupper(chr($fchar));
	$s = iconv("UTF-8", "GBK", $s0);
	$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
	if ($asc >= -20319 and $asc <= -20284)
		return "A";
	if ($asc >= -20283 and $asc <= -19776)
		return "B";
	if ($asc >= -19775 and $asc <= -19219)
		return "C";
	if ($asc >= -19218 and $asc <= -18711)
		return "D";
	if ($asc >= -18710 and $asc <= -18527)
		return "E";
	if ($asc >= -18526 and $asc <= -18240)
		return "F";
	if ($asc >= -18239 and $asc <= -17923)
		return "G";
	if ($asc >= -17922 and $asc <= -17418)
		return "H";
	if ($asc >= -17417 and $asc <= -16475)
		return "J";
	if ($asc >= -16474 and $asc <= -16213)
		return "K";
	if ($asc >= -16212 and $asc <= -15641)
		return "L";
	if ($asc >= -15640 and $asc <= -15166)
		return "M";
	if ($asc >= -15165 and $asc <= -14923)
		return "N";
	if ($asc >= -14922 and $asc <= -14915)
		return "O";
	if ($asc >= -14914 and $asc <= -14631)
		return "P";
	if ($asc >= -14630 and $asc <= -14150)
		return "Q";
	if ($asc >= -14149 and $asc <= -14091)
		return "R";
	if ($asc >= -14090 and $asc <= -13319)
		return "S";
	if ($asc >= -13318 and $asc <= -12839)
		return "T";
	if ($asc >= -12838 and $asc <= -12557)
		return "W";
	if ($asc >= -12556 and $asc <= -11848)
		return "X";
	if ($asc >= -11847 and $asc <= -11056)
		return "Y";
	if ($asc >= -11055 and $asc <= -10247)
		return "Z";
	return null;
}

function get_folder_name($id) {

	if ($id == 1) {
		return "收件箱";
	}
	if ($id == 2) {
		return "已发送";
	}
	if ($id == 3) {
		return "草稿箱";
	}
	if ($id == 4) {
		return "已删除";
	}
	if ($id == 5) {
		return "垃圾邮件";
	}

	$model = D("UserFolder");
	$result = $model -> where("id=$id") -> getField("name");
	if ($result) {
		return $result;
	} else {
		return null;
	}
}

function mail_org_string($vo) {
	$count = 0;
	if (!empty($vo['sender_check']) && $count < 1) {
		$count++;
		if ($vo["sender_option"] == 1) {
			$str1 = "包含";
		} else {
			$str1 = "不包含";
		}
		$str2 = $vo['sender_key'];

		$str3 = get_folder_name($vo["to"]);

		$html = "发件人" . $str1 . " " . $str2 . " 则 : 移动到 " . $str3;
	};

	if (!empty($vo['domain_check']) && $count < 1) {
		$count++;
		if ($vo["domain_option"] == 1) {
			$str1 = "包含";
		} else {
			$str1 = "不包含";
		}
		$str2 = $vo['domain_key'];

		$str3 = get_folder_name($vo["to"]);

		$html = "发件域" . $str1 . " " . $str2 . " 则 : 移动到 " . $str3;
	};

	if (!empty($vo['recever_check']) && $count < 1) {
		$count++;
		if ($vo["recever_option"] == 1) {
			$str1 = "包含";
		} else {
			$str1 = "不包含";
		}
		$str2 = $vo['recever_key'];

		$str3 = get_folder_name($vo["to"]);

		$html = "收件人" . $str1 . " " . $str2 . " 则 : 移动到 " . $str3;
	};

	if (!empty($vo['title_check']) && $count < 1) {
		$count++;
		if ($vo["title_option"] == 1) {
			$str1 = "包含";
		} else {
			$str1 = "不包含";
		}
		$str2 = $vo['title_key'];

		$str3 = get_folder_name($vo["to"]);

		$html = "标题中" . $str1 . " " . $str2 . " 则 : 移动到 " . $str3;
	};
	if ($count > 1) {
		$html .= " 等";
	}
	return $html;
}

function status($status) {
	if ($status == 0) {
		return "启用";
	}
	if ($status == 1) {
		return "禁用";
	}
}

function crm_status($status) {
	if ($status == 0) {
		return "未审核";
	}
	if ($status == 1) {
		return "通过";
	}
	if ($status == 2) {
		return "拒绝";
	}
}

function todo_status($status) {
	if ($status == 1) {
		return "尚未进行";
	}
	if ($status == 2) {
		return "正在进行";
	}
	if ($status == 3) {
		return "完成";
	}
}

function mb_unserialize($serial_str) {
	$out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str);
	return unserialize($out);
}

function get_sid() {
	return md5(bin2hex(time()) . rand_string());
}

function get_position_name($id) {
	$data = D('UserView') -> find($id);
	return $data['position_name'];
}

function send_push($data, $user_list, $time = null, $type = 'text') {

	$model = M("Push");
	if (empty($time)) {
		$model -> time = time();
	} else {
		$model -> time = $time;
	}
	if (is_array($user_list)) {
		foreach ($user_list as $val) {
			$model -> data = json_encode($data, JSON_UNESCAPED_UNICODE);
			$model -> user_id = $val;
			$model -> add();
		}
	} else {
		if (empty($user_list)) {
			$model -> user_id = get_user_id();
			$user_list = array(get_user_id());
		} else {
			$model -> user_id = $user_list;
			$user_list = array($user_list);
		}
		$model -> data = json_encode($data, JSON_UNESCAPED_UNICODE);
		$model -> add();
	}

	$ws_push_config = get_system_config('ws_push_config');
	if (!empty($ws_push_config)) {
		$ws_push_config = array_filter(explode(',', $ws_push_config));
		if (in_array($data['type'], $ws_push_config)) {
			@send_ws($data, $user_list);
		}
	}

	$sms_push_config = get_system_config('sms_push_config');
	if (!empty($sms_push_config)) {
		$sms_push_config = array_filter(explode(',', $sms_push_config));
		if (in_array($data['type'], $sms_push_config)) {
			//@send_sms($data, $user_list);
		}
	}

	$weixin_push_config = get_system_config('weixin_push_config');
	if (!empty($weixin_push_config)) {
		$weixin_push_config = array_filter(explode(',', $weixin_push_config));
		if (in_array($data['type'], $weixin_push_config)) {
			@send_weixin($data, $user_list);
		}
	}
}

function send_ws($data, $user_list) {
	// $client = stream_socket_client('tcp://127.0.0.1:7273');
	// if (!$client) {
		// //exit("can not connect");
		// return;
	// }
	// // 模拟超级用户，以文本协议发送数据，注意文本协议末尾有换行符（发送的数据中最好有能识别超级用户的字段），这样在Event.php中的onMessage方法中便能收到这个数据，然后做相应的处理
	// $msg['type'] = "say";
	// $msg['to_client_id'] = "all";
	// $msg['to_client_name'] = $user_list;
	// $msg['content'] = $data;
	// $msg['room_id'] = "1";
	// fwrite($client, json_encode($msg) . "\n");

	// 推送的url地址，上线时改成自己的服务器地址
	$push_api_url = "http://127.0.0.1:2121/";
	$post_data = array("type" => "publish", "to" => implode(',', $user_list));
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $push_api_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$return = curl_exec($ch);
	curl_close($ch);
	//@var_export($return);
}

function send_weixin($data, $user_list) {
	// 这样可以按照企业号的发送格式发送图文、文件上、声音等多媒体信息了,curl不支持多维数组，转换成json传递数据|Terry
	$msg = json_encode($data, JSON_UNESCAPED_UNICODE);

	$where['id'] = array('in', $user_list);
	$openid = M('User') -> where($where) -> getField('openid', true);
	$openid = implode('|', array_filter($openid));

	// 这里$url不加/oa/的话测试图文会传递到weixin的index控制器|Terry
	$url = get_system_config('weixin_site_url') . "/index.php?m=Weixin&c=Oa&a=send";
	$type = 'news';

	$params['content'] = $msg;
	$params['openid'] = $openid;

	//$params['type'] = $type;

	$opts[CURLOPT_TIMEOUT_MS] = 1000;
	$opts[CURLOPT_RETURNTRANSFER] = 1;

	$opts[CURLOPT_URL] = $url;
	$opts[CURLOPT_POST] = 1;
	$opts[CURLOPT_POSTFIELDS] = $params;

	/* 初始化并执行curl请求 */
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = @curl_exec($ch);
	$error = curl_error($ch);
	curl_close($ch);

	return $data;
}

function send_sms($data, $user_list) {

	header("Content-Type: text/html; charset=utf-8");
	$msg = '【SIAS】亲爱的同事您好，你找回的OA财务密码：ABCDEF，请妥善保管。';
	// $url = 'http://192.168.100.9:9080/OpenMasService?WSDL';
	// $message = $msg;
	// $extendCode = "26";
	//
	// //自定义扩展代码（模块）
	// $ApplicationID = "OA";
	//
	// //账号
	// $Password = "3hxiuE8bWNFC";
	//
	// //密码
	// if (is_array($user_list)) {
	// $where['id'] = array('in', $user_list);
	// } else {
	// $where['id'] = array('eq', $user_list);
	// }
	//
	// $mobile_list = M("User") -> where($where) -> getField('mobile_tel', true);
	// $destinationAddresses = $mobile_list;
	//
	// //手机号码
	// $paras = array('destinationAddresses' => $destinationAddresses, 'message' => $message, 'extendCode' => $extendCode, 'applicationId' => $ApplicationID, 'password' => $Password);
	// $client = new soapclient($url);
	// $result = $client -> SendMessage3($paras);

	$sms_user = 'jkwl110';
	$sms_password = 'jkwl11033';
	if (is_array($user_list)) {
		$where['id'] = array('in', $user_list);
	} else {
		$where['id'] = array('eq', $user_list);
	}

	//$sms_user_list = M("User") -> where($where) -> getField('mobile_tel', true);
	//$sms_user_list=implode(",", $sms_user_list);
	$sms_user_list = $user_list;
	$url = "http://sh2.ipyy.com/sms.aspx?action=send&userid=&account={$sms_user}&password={$sms_password}&mobile={$sms_user_list}&content={$msg}&sendTime=&extno=";

	$result = file_get_contents($url);
	dump($result);
}

function get_emp_pic($id) {

	$where['id'] = array('eq', $id);
	$data = M("User") -> where($where) -> getField("pic");
	if (empty($data)) {
		$data = "./Uploads/emp_pic/no_avatar.jpg";
	} else {

	}
	return $data;
}

function task_status($status) {
	if ($status == 0) {
		return "待处理";
	}
	if ($status == 10) {
		return "进行中";
	}
	if ($status == 20) {
		return "已完成";
	}
	if ($status == 21) {
		return "已转交";
	}
	if ($status == 22) {
		return "已拒绝";
	}
	if ($status == 30) {
		return "已完成";
	}
}

function task_log_status($status) {
	if ($status == 10) {
		return "进行中";
	}
	if ($status == 1) {
		return "已接受";
	}
	if ($status == 2) {
		return "进行中";
	}
	if ($status == 3) {
		return "已完成";
	}
	if ($status == 4) {
		return "已转交";
	}
	if ($status == 5) {
		return "不接受";
	}
}

function finish_rate($rate) {
	if ($rate == 0) {
		return "任务未开始执行";
	}
	if ($rate > 0 and $rate < 100) {
		return "任务已完成$rate%";
	}
	if ($rate == 100) {
		return "任务已完成";
	}
}

function is_submit($val) {
	if ($val == 0) {
		return "临时保管";
	}
	if ($val == 1) {
		return "已提交";
	}
}

function array_to_obj($e) {
	if (gettype($e) != 'array')
		return;
	foreach ($e as $k => $v) {
		if (gettype($v) == 'array' || getType($v) == 'object')
			$e[$k] = (object)array_to_obj($v);
	}
	return (object)$e;
}

function obj_to_array($e) {
	$e = (array)$e;
	foreach ($e as $k => $v) {
		if (gettype($v) == 'resource')
			return;
		if (gettype($v) == 'object' || gettype($v) == 'array')
			$e[$k] = (array)obj_to_array($v);
	}
	return $e;
}

function sign_type($val) {
	if ($val == 'sign_in') {
		return "签到";
	}
	if ($val == 'sign_out') {
		return "签退";
	}
	if ($val == 'outside') {
		return "外勤";
	}
}

function conv_baidu_map(&$lat, &$lng) {
	$url = "http://api.map.baidu.com/geoconv/v1/?coords=$lng,$lat&from=1&to=5&ak=EE6745c36d96321e90b7015f3de4a4ee";
	$result = json_decode(file_get_contents($url));
	$lat = $result -> result[0] -> x;
	$lng = $result -> result[0] -> y;
	$data['lat'] = $result -> result[0] -> x;
	$data['lng'] = $result -> result[0] -> y;
	return $data;
}

function get_location($lat, $lng) {
	conv_baidu_map($lat, $lng);

	$url = "http://api.map.baidu.com/geocoder/v2/?ak=EE6745c36d96321e90b7015f3de4a4ee&callback=renderReverse&location=$lng,$lat&output=json";
	$json = file_get_contents($url);
	$json = str_replace("renderReverse&&renderReverse(", '', $json);
	$json = substr($json, 0, -1);
	$result = json_decode($json);
	return $result -> result -> formatted_address;
}

function del_html_tag($html) {
	$qian = array(" ", "　", "\t", "\n", "\r");
	$hou = array("", "", "", "", "");
	$html = strip_tags($html);
	return str_replace($qian, $hou, $html);
}

function get_work_order($user_id, $date) {

	$where['executor'] = array('eq', $user_id);
	$where['request_arrive_time'] = array( array('gt', $date . " 00:00"), array('lt', $date . " 23:59"));
	$where['is_del'] = array('eq', 0);
	//$where['status'] = array('eq', 3);
	$list = D('WorkOrderLogView') -> where($where) -> select();

	foreach ($list as $val) {
		$request_arrive_time = $val['request_arrive_time'];
		if ($request_arrive_time > $date . " 12:00:00") {
			$data_pm_name[] = $val['name'];
			$data_pm_content[] = $val['order_type'];
		} else {
			$data_am_name[] = $val['name'];
			$data_am_content[] = $val['order_type'];
		}
	}

	$am_name = $data_am_name[0];
	$am_content = $data_am_content[0];

	$pm_name_1 = $data_pm_name[0];
	$pm_content_1 = $data_pm_content[0];

	$pm_name_2 = $data_pm_name[1];
	$pm_content_2 = $data_pm_content[1];

	$html = "<div><div class=\"col-sm-4\"><p>{$am_name}</p><p>{$am_content}</p></div><div class=\"col-sm-4\"><p>{$pm_name_1}</p><p>{$pm_content_1}</p></div><div class=\"col-sm-4\"><p>{$pm_name_2}</p><p>{$pm_content_2}</p></div></div>";
	return $html;
}

function arrToStr($arr) {
	foreach ($arr as $v) {
		$v = implode("\n", $v);
		//可以用implode将一维数组转换为用逗号连接的字符串
		$temp[] = $v;
	}
	$t = implode("\n\n", $temp);
	return $t;
}

function conv_flot($data) {
	$return = '[';
	if (empty($data)) {
		return "[]";
	} else {
		foreach ($data as $key => $val) {
			$return .= "[{$key}000,{$val}],";
		}
	}
	return substr($return, 0, -1) . ']';
}

function is_public($id) {
	if ($id == 1) {
		return "公开";
	}
	if ($id == 0) {
		return "私有";
	}
}

function get_push_agent_id($type) {
	$msg_push_config = array_filter(explode(";", get_system_config('msg_push_config')));
	foreach ($msg_push_config as $val) {
		$tmp = explode("=", $val);
		list($msg_type, $push_agent_id) = $tmp;
		if ($msg_type == $type) {
			return $push_agent_id;
		}
	}
	return get_system_config('oa_agent_id');
}

function get_flow_receive_is_read($id) {
	$where['flow_id'] = array('eq', $id);
	$where['step'] = array('eq', 100);
	$where['is_read'] = array('eq', 0);

	$count = M("FlowLog") -> where($where) -> count();
	if ($count) {
		return bold;
	};
}

//签批时判断是否是已经选择的领导
function is_disable($gov_doc_id, $user_id) {
	$model = M('gov_doc_log');
	$where['gov_doc_id'] = $gov_doc_id;
	$list = $model -> where($where) -> select();

	foreach ($list as $v) {
		if ($user_id == $v['user_id']) {
			return "disabled='disabled'";
		}
	}
}

//获取url
function get_nav_url($url) {
	switch ($url) {
		case 'http://' === substr($url, 0, 7) :
		case '#' === substr($url, 0, 1) :
			break;
		default :
			$url = U($url);
			break;
	}
	return $url;
}

function get_top_menu_id($url, $menu = null) {
	if (empty($menu)) {
		$menu = D("Node") -> access_list();
		$menu = sort_by($menu, 'sort');
	}
	$menu = tree_to_list(list_to_tree($menu));
	foreach ($menu as $key => $val) {
		$node_url = str_replace('#', '', $val['url']);
		if ($node_url == $url) {
			$return = $val['id'];
		}
	}

	arsort($menu);
	foreach ($menu as $key => $val) {
		if ($val['id'] == $return) {
			if ($val['pid'] != 0) {
				$return = $val['pid'];
			}
		}
	}
	return $return;
}
?>