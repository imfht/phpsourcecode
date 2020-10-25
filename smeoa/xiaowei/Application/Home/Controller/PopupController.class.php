<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class PopupController extends HomeController {
	protected $config = array('app_type' => 'asst');
	//过滤查询字段
	private $position;
	private $rank;
	private $dept;

	function _search_filter(&$map) {
		$map['name'] = array('like', "%" . $_POST['name'] . "%");
		$map['letter'] = array('like', "%" . $_POST['letter'] . "%");
		$map['is_del'] = array('eq', '0');
		if (!empty($_POST['group'])) {
			$map['group'] = $_POST['group'];
		}
		$map['user_id'] = array('eq', get_user_id());
	}

	function read($id) {
		$type = I('type');
		switch ($type) {
			case "dept" :
				$model = M("Dept");
				$dept = tree_to_list(list_to_tree( M("Dept") -> where('is_del=0') -> select(), $id));
				$dept = rotate($dept);
				$dept = implode(",", $dept['id']) . ",$id";

			case "emp" :
				$model = M("Dept");
				$dept = tree_to_list(list_to_tree( M("Dept") -> where('is_del=0') -> select(), $id));
				$dept = rotate($dept);
				$dept = implode(",", $dept['id']) . ",$id";

				$model = D("UserView");

				$where['dept_id'] = array('in', $dept);
				$where['is_del'] = array('eq', 0);
				$data = $model -> where($where) -> select();
				break;

			case "group" :
				$user_list = D("Group") -> get_user_list($id);

				$model = D("UserView");
				if (!empty($user_list)) {
					$where['id'] = array('in', $user_list);
					$where['is_del'] = array('eq', 0);
				} else {
					$where['_string'] = '1=2';
				}

				$data = $model -> where($where) -> select();
				break;

			case "position" :
				$model = D("UserView");
				$where['position_id'] = array('eq', $id);
				$where['is_del'] = array('eq', 0);
				$data = $model -> where($where) -> select();
				break;

			case "personal" :
				$model = D("UserTag");
				if ($id == "#") {
					$data = $model -> get_data_list("Contact");
					$data = rotate($data);
					$data = $data['row_id'];
					if (!empty($data)) {
						$where['id'] = array('not in', implode(",", $data));
					} else {
						$where['_string'] = '1=2';
					}
				} else {
					$test = $model;
					$data = $model -> get_data_list("Contact", $id);
					$data = rotate($data);

					$data = $data['row_id'];

					if (!empty($data)) {
						$where['id'] = array('in', implode(",", $data));
					} else {
						$where['_string'] = '1=2';
					}
				}
				$model = M("Contact");
				$where['is_del'] = array('eq', 0);
				$data = $model -> where($where) -> field('id,name,position as position_name,email') -> select();
				break;
			default :
		}
		$new = array();
		$return['status'] = 1;
		$return['data'] = $data;
		$this -> ajaxReturn($return);
	}

	private function _dept_list() {
		$model = M("Dept");
		$list = array();
		$list = $model -> where('is_del=0') -> field('id,pid,name') -> order('sort asc') -> select();
		$list = list_to_tree($list);
		$this -> assign('list_dept', popup_tree_menu($list));
	}

	private function _position_list() {
		$model = M("Position");
		$list = array();
		$list = $model -> field('id,name') -> order('sort asc') -> select();
		$list = list_to_tree($list);
		$this -> assign('list_position', popup_tree_menu($list));
	}

	private function _group_list() {
		$model = M("Group");
		$list = array();
		$where['user_id'] = array('eq', get_user_id());
		$list = $model -> field('id,name') -> order('sort asc') -> select();
		$list = list_to_tree($list);
		$this -> assign('list_group', popup_tree_menu($list));
	}

	private function _tag_list() {
		$model = D("UserTag");
		$tag_list = $model -> get_tag_list("id,name", "Contact");
		$tag_list['#'] = "未分组";
		$this -> assign("list_personal", $tag_list);
	}

	function contact() {
		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _dept_list();
		$this -> _position_list();
		$this -> _tag_list();
		$this -> _group_list();

		$this -> assign('type', 'dept');
		$this -> display();
		return;
	}

	function mobile() {
		$node = D("Dept");
		$menu = array();
		$menu = $node -> field('id,pid,name') -> where("is_del=0") -> order('sort asc') -> select();
		$tree = list_to_tree($menu);
		$list = tree_to_list($tree);
		$this -> assign('menu', popup_tree_menu2($tree));
		$this -> display('mobile');
	}

	function auth() {
		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _dept_list();
		$this -> _position_list();

		$this -> assign('type', 'dept');
		$this -> display();
		return;
	}

	function upload() {
		$this -> _upload();
	}

	function emp() {
		$this -> auth();
	}

	function avatar() {
		$id = I('id');
		if (!empty($id)) {
			$this -> assign("id", $id);
			$this -> assign("pic", M("User") -> where("id=$id") -> getField('pic'));
		}
		$this -> display();
	}


	function depts() {
		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);
		$this -> _dept_list();
		$this -> display();
		return;
	}

	function scope() {
		$this -> actor();
	}

	function actor() {
		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _dept_list();
		$this -> _position_list();
		$this -> _group_list();

		$this -> assign('type', 'dept');
		$this -> display();
		return;
	}

	function task() {
		$this -> actor();		
	}

	function work_order() {
		$this -> actor();
	}

	function confirm() {

		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _dept_list();
		$this -> _position_list();

		$this -> assign('type', 'dept');
		$this -> display();
		return;
	}

	function recever() {
		$this -> actor();
	}

	function refer() {
		$this -> actor();
	}

	function flow() {

		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		$model = M("DeptGrade");
		$list = array();
		$list = $model -> field('id,name') -> order('sort asc') -> select();
		$list = list_to_tree($list);
		$this -> assign('list_dept_grade', sub_tree_menu($list));

		$this -> _dept_list();
		$this -> _position_list();

		$this -> assign('type', 'dgp');
		$this -> display();
		return;
	}

	function popup_depts() {
		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _dept_list();
		$this -> display();
		return;
	}

	function position() {
		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _position_list();
		$this -> display();
		return;
	}

	function resize_img() {
		if (!$image = $_POST["img"]) {
			$result['result_code'] = 101;
			$result['result_des'] = "图片不存在";
		} else {

			$real_img = $_SERVER['DOCUMENT_ROOT'] . $image;

			$info = get_img_info($real_img);

			if (!$info) {
				$result['result_code'] = 102;
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$result['result_des'] = $protocol . $_SERVER['HTTP_HOST'] . $image;
			} else {
				$max_width = 440;
				if ($info['type'] == 'jpg' || $info['type'] == 'jpeg') {
					$im = imagecreatefromjpeg($real_img);
				}
				if ($info['type'] == 'gif') {
					$im = imagecreatefromgif($real_img);
				}
				if ($info['type'] == 'png') {
					$im = imagecreatefrompng($real_img);
				}
				if ($info['width'] <= $max_width) {
					$rate = 1;
				} else {
					$rate = $info['width'] / $max_width;
					if ($info['width'] > $info['height']) {
						$max_height = intval($info['height'] / ($info['width'] / $max_width));
					} else {
						$max_width = intval($info['width'] / ($info['height'] / $max_height));
					}
				}

				$x = $_POST["x"];
				$y = $_POST["y"];
				$w = $_POST["w"];
				$h = $_POST["h"];

				$width = $srcWidth = $info['width'];
				$height = $srcHeight = $info['height'];
				$type = empty($type) ? $info['type'] : $type;
				$type = strtolower($type);
				unset($info);
				//创建缩略图
				if ($type != 'gif' && function_exists('imagecreatetruecolor'))
					$thumbImg = imagecreatetruecolor($width, $height);
				else
					$thumbImg = imagecreate($width, $height);
				// 复制图片
				if (function_exists("imagecopyresampled"))
					imagecopyresampled($thumbImg, $im, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
				else
					imagecopyresized($thumbImg, $im, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
				if ('gif' == $type || 'png' == $type) {
					$background_color = imagecolorallocate($thumbImg, 0, 255, 0);
					imagecolortransparent($thumbImg, $background_color);
				}
				// 对jpeg图形设置隔行扫描
				if ('jpg' == $type || 'jpeg' == $type)
					imageinterlace($thumbImg, 1);

				$emp_pic_path = C('EMP_PIC_PATH');
				if (!is_dir($emp_pic_path)) {
					mkdir($emp_pic_path, 0777, true);
					chmod($emp_pic_path, 0777);
				}

				// 生成图片
				$imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
				$id = I('id');

				$thumbname = $emp_pic_path . $id . "." . $type;

				$imageFun($thumbImg, $thumbname);

				$thumbImg_120 = imagecreatetruecolor(120, 120);
				imagecopyresampled($thumbImg_120, $thumbImg, 0, 0, intval($x * $rate), intval($y * $rate), intval(120 * 1), intval(120 * 1), intval($w * $rate), intval($h * $rate));
				$imageFun($thumbImg_120, $thumbname);

				imagedestroy($thumbImg);
				imagedestroy($im);

				$result['result_code'] = 1;
				$result['result_des'] = $thumbname;
			}
		}
		echo json_encode($result);
	}

	function message() {
		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _dept_list();
		$this -> _position_list();
		$this -> _group_list();
		$this -> assign('type', 'dept');
		$this -> display();
		return;
	}

	function json() {
		header("Content-Type:text/html; charset=utf-8");
		$type = I('type'); ;
		$key = $_REQUEST['key'];

		$model = M("User");
		$where['name'] = array('like', "%" . $key . "%");
		$where['letter'] = array('like', "%" . $key . "%");
		$where['email'] = array('like', "%" . $key . "%");
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['is_del'] = array('eq', 0);
		$dept = $model -> where($map) -> field('id,name,email') -> select();

		if ($type == "all") {
			$where = array();
			$map = array();
			$model = M("Contact");
			$where['name'] = array('like', "%" . $key . "%");
			$where['letter'] = array('like', "%" . $key . "%");
			$where['email'] = array('like', "%" . $key . "%");
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
			$map['email'] = array('neq', '');
			$map['user_id'] = array('eq', get_user_id());
			$personal = $model -> where($map) -> field('id,name,email') -> select();
		}
		if (empty($dept)) {
			$dept = array();
		}
		if (empty($personal)) {
			$personal = array();
		}
		$contact = array_merge_recursive($dept, $personal);
		exit(json_encode($contact));
	}

	function link_select($data) {
		header("Content-Type:text/html; charset=utf-8");
		switch ($data) {
			case 'dept' :
				$model = M("Dept");
				$list = $model -> where('is_del=0') -> field('id,pid,name') -> order('sort asc') -> select();
				exit(json_encode($list));
				break;
			default :
				break;
		}
	}

	public function flow_dept() {
		$node = M("Dept");
		$menu = array();
		$menu = $node -> where('is_del=0') -> field('id,pid,name') -> order('sort asc') -> select();

		$tree = list_to_tree($menu);
		$this -> assign('menu', popup_tree_menu($tree));
		$pid = array();
		$this -> assign('pid', $pid);
		$this -> display();
	}

	public function flow_customer() {
		$node = M("Dept");
		$menu = array();
		$menu = $node -> where('is_del=0') -> field('id,pid,name') -> order('sort asc') -> select();

		$tree = list_to_tree($menu);
		$this -> assign('menu', popup_tree_menu($tree));
		$pid = array();
		$this -> assign('pid', $pid);
		$this -> display();
	}
	
	public function flow_emp() {
		$this->auth();
	}	
}
?>