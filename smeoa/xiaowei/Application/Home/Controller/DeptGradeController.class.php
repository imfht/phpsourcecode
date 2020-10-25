<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class DeptGradeController extends HomeController {
	//app 类型
	protected $config = array('app_type' => 'master');

	function _search_filter(&$map) {
		$keyword=I('keyword');
		if (!empty($keyword)) {
			$map['grade_no|name'] = array('like', "%" . $keyword . "%");
		}
	}

	public function index() {
		$model = M("DeptGrade");
		$list = $model -> order('sort') -> select();
		$this -> assign('list', $list);
		$this -> display();
	}

	function del($id) {		
		$this -> _destory($id);
	}

}
?>