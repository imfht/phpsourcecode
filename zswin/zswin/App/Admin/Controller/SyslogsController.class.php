<?php
namespace Admin\Controller;
class SyslogsController extends CommonController {
	function _filter(&$map) {
		$map['modulename'] = array('like', "%" . I('keyword') . "%");
		$map['actionname'] = array('like', "%" . I('keyword') . "%");
		$map['opname'] = array('like', "%" . I('keyword') . "%");
		$map['message'] = array('like', "%" . I('keyword') . "%");
		$map['username'] = array('like', "%" . I('keyword') . "%");
		$map['userid'] = array('like', "%" . I('keyword') . "%");
		$map['userip'] = array('like', "%" . I('keyword') . "%");
		$map['_logic'] = 'or';
		
	}




}

?>