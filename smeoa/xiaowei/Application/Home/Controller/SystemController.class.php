<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class SystemController extends HomeController {
	//过滤查询字段
	protected $config = array('app_type' => 'asst');
	function _search_filter(&$map) {
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$map['type|name|code'] = array('like', "%" . $keyword . "%");
		}
	}

	function index() {
		$where_user['is_del'] = array('eq', 0);
		$user_count = M("User") -> where($where_user) -> count();
		$this -> assign('user_count', $user_count);

		$where_dept['is_del'] = array('eq', 0);
		$dept_count = M("Dept") -> where($where_dept) -> count();
		$this -> assign('dept_count', $dept_count);

		$file_count = M("File") -> count();
		$this -> assign('file_count', $file_count);

		$file_spage = M("File") -> sum('size');
		$this -> assign('file_spage', $file_spage);

		$where_size['type'] = array('eq', 1);
		//$where_size['time']=array('gt',time()-2592000);
		$file_size = M("SystemLog") -> where($where_size) -> getField('time,data');

		$file_size = conv_flot($file_size);
		$this -> assign('file_size', $file_size);
		$this -> display();
	}

	function get_flot_data() {
		$range=I('range');
		
		switch ($range) {
			case 'm':
				$offset=mktime(0, 0 , 0,date("m")-1,date("d"),date("Y"));
				break;

			case 'q':
				$offset=mktime(0, 0 , 0,date("m")-3,date("d"),date("Y"));
				break;
				
			case 'y':
				$offset=mktime(0, 0 , 0,date("m")-112,date("d"),date("Y"));
				break;							
			default:
				
				break;
		}
		$where_size['type'] = array('eq', 1);
		$where_size['time']=array('gt',$offset);
		$file_size = M("SystemLog") -> where($where_size) -> getField('time,data');
		$file_size = conv_flot($file_size);
		
		$where_count['type'] = array('eq', 2);
		$where_count['time']=array('gt',$offset);
		$file_count = M("SystemLog") -> where($where_count) -> getField('time,data');
		$file_count = conv_flot($file_count);
		
		$return['file_size']=$file_size;
		$return['file_count']=$file_count;
		$this->ajaxReturn($return);				
	}

	function RandAbc($length = "") {//返回随机字符串
		$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		return str_shuffle($str);
	}

	function get_auth() {
		$server_info = $this -> _SERVER('SERVER_NAME') . '|' . $this -> _SERVER('REMOTE_ADDR');
		$server_info .= '|' . $this -> _SERVER('DOCUMENT_ROOT');

		$result = @file_get_contents('http://www.smeoa.com/get_auth.php?' . base64_encode($server_info));
		return $result;
	}

	function _GET($n) {
		return isset($_GET[$n]) ? $_GET[$n] : NULL;
	}

	function _SERVER($n) {
		return isset($_SERVER[$n]) ? $_SERVER[$n] : '[undefine]';
	}

}
?>