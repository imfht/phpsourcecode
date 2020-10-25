<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;
use Think\Controller;

class HomeController extends Controller {
	protected $config = array('app_type' => 'asst');

	function _initialize() {
		$auth_id = session(C('USER_AUTH_KEY'));
		if (!isset($auth_id)) {
			//跳转到认证网关
			redirect(U(C('USER_AUTH_GATEWAY')));
		}
		$this -> _assign_menu();
		$this -> _assign_badge_count();
		$this -> _system_log();
	}

	/**显示top menu及 left menu **/
	protected function _assign_menu() {
		$user_id = get_user_id();

		$model = D("Node");
		$top_menu_list = $model -> get_top_menu($user_id);
		if (empty($top_menu_list)) {
			$this -> assign('jumpUrl', U("Public/logout"));
			$this -> error("没有权限");
		}

		$this -> assign('top_menu', $top_menu_list);

		//读取数据库模块列表生成菜单项
		$menu = D("Node") -> access_list();
		$system_folder_menu = D("SystemFolder") -> get_folder_menu();
		$user_folder_menu = D("UserFolder") -> get_folder_menu();

		$menu = array_merge($menu, $system_folder_menu, $user_folder_menu);
		$menu = sort_by($menu, 'sort');

		$return_url = I('get.return_url');
		if (!empty($return_url)) {
			cookie('return_url', U($return_url));
			$top_menu = get_top_menu_id($return_url, $menu);
			cookie('top_menu', $top_menu);
		} else {
			$top_menu = cookie('top_menu');
		}

		if (!empty($top_menu)) {
			$top_menu_name = $model -> where("id=$top_menu") -> getField('name');
			$this -> assign("top_menu_name", $top_menu_name);
			$this -> assign("title", get_system_config("system_name") . "-" . $top_menu_name);

			$left_menu = list_to_tree($menu, $top_menu);
			$this -> assign('left_menu', $left_menu);
		} else {
			$this -> assign("title", get_system_config("system_name"));
		}
	}

	protected function _assign_badge_count() {
		$node_list = D("Node") -> access_list();
		$system_folder_menu = D("SystemFolder") -> get_folder_menu();
		$user_folder_menu = D("UserFolder") -> get_folder_menu();
		$node_list = array_merge($node_list, $system_folder_menu, $user_folder_menu);

		foreach ($node_list as $val) {
			$badge_function = $val['badge_function'];
			if (!empty($badge_function) and function_exists($badge_function) and ($badge_function != 'badge_sum')) {
				if ($badge_function == 'badge_count_system_folder' or $badge_function == 'badge_count_user_folder') {
					$badge_count[$val['id']] = $badge_function($val['fid']);
				} else {
					$badge_count[$val['id']] = $badge_function();
				}
			}
		};

		//$node_tree = list_to_tree($node_list);
		foreach ($node_list as $key => $val) {
			if ($val['badge_function'] == 'badge_sum') {
				$child_menu = list_to_tree($node_list, $val['id']);
				$child_menu = tree_to_list($child_menu);
				//dump($child_menu);
				$child_menu_id = rotate($child_menu);
				$count = 0;
				if (isset($child_menu_id['id'])) {
					$child_menu_id = $child_menu_id['id'];
					$count = 0;
					foreach ($child_menu_id as $k1 => $v1) {
						if (!empty($badge_count[$v1])) {
							$count += $badge_count[$v1];
						}
					}
				}
				$badge_sum[$val['id']] = $count;
			}
		};
		if (!empty($badge_count)) {
			if (!empty($badge_sum)) {
				$total = $badge_count + $badge_sum;
			} else {
				$total = $badge_count;
			}
			$this -> assign('badge_count', $total);
		}
	}

	function _system_log() {
		$system_log_time = S('system_log_time');
		if (empty($system_log_time)) {
			$flag = true;
		} else {
			$flag = (time() - S('system_log_time')) > 24 * 3600;
		}
		if ($flag) {
			$time = time();
			S('system_log_time', $time);
			$data['time'] = $time;
			$data['type'] = 1;
			$data['data'] = M("File") -> count();

			M("SystemLog") -> add($data);

			$data['type'] = 2;
			$data['data'] = M("File") -> sum('size') / 1024 / 1024;

			M("SystemLog") -> add($data);
		}
	}

	/**列表页面 **/
	function index() {
		$this -> _index();
	}

	/**查看页面 **/
	function read($id) {
		$this -> _edit($id);
	}

	/**编辑页面 **/
	function edit($id) {
		$this -> _edit($id);
	}

	/** 保存操作  **/
	function save() {
		$this -> _save();
	}

	/**列表页面 **/
	protected function _index($name = CONTROLLER_NAME) {
		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}
		$model = D($name);
		if (!empty($model)) {
			$this -> _list($model, $map);
		}
		$this -> display();
	}

	/**编辑页面 **/
	protected function _edit($id, $name = CONTROLLER_NAME) {
		$model = M($name);
		$vo = $model -> find($id);
		if (IS_AJAX) {
			if ($vo !== false) {// 读取成功
				$return['data'] = $vo;
				$return['status'] = 1;
				$return['info'] = "读取成功";
				$this -> ajaxReturn($return);
			} else {
				$return['status'] = 0;
				$return['info'] = "读取错误";
				$this -> ajaxReturn($return);
			}
		}
		$this -> assign('vo', $vo);
		$this -> display();
		return $vo;
	}

	protected function _save($name = CONTROLLER_NAME) {
		$opmode = I('opmode');
		switch($opmode) {
			case "add" :
				$this -> _insert($name);
				break;
			case "edit" :
				$this -> _update($name);
				break;
			default :
				$this -> error("非法操作");
		}
	}

	/** 插入新新数据  **/
	protected function _insert($name = CONTROLLER_NAME) {

		$model = D($name);
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}

		/*保存当前数据对象 */
		$list = $model -> add();
		if ($list !== false) {//保存成功
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

	/** 删除标记  **/
	protected function _del($id, $name = CONTROLLER_NAME, $return_flag = false) {
		$model = M($name);
		if (!empty($model)) {
			if (isset($id)) {
				if (is_array($id)) {
					$where['id'] = array("in", array_filter($id));
				} else {
					$where['id'] = array('in', array_filter(explode(',', $id)));
				}
				$result = $model -> where($where) -> setField("is_del", 1);
				if ($return_flag) {
					return $result;
				}
				if ($result !== false) {
					$this -> assign('jumpUrl', get_return_url());
					$this -> success("成功删除{$result}条!");
				} else {
					$this -> error('删除失败!');
				}
			} else {
				$this -> error('没有可删除的数据!');
			}
		} else {
			$this -> error('没有可删除的数据!');
		}
	}

	/** 永久删除数据  **/
	protected function _destory($id, $name = CONTROLLER_NAME, $return_flag = false) {

		$model = M($name);
		if (is_array($id)) {
			$where['id'] = array("in", array_filter($id));
		} else {
			$where['id'] = array('in', array_filter(explode(',', $id)));
		}

		$app_type = $this -> config['app_type'];

		if ($app_type == "personal") {
			$where['user_id'] = get_user_id();
		}

		if (in_array('add_file', $model -> getDbFields())) {
			$file_list = $model -> where($where) -> getField("add_file", true);
			$file_list = array_filter(explode(";", implode(';', $file_list)));
			if (!empty($file_list)) {
				$this -> _destory_file($file_list);
			}
		};

		$result = $model -> where($where) -> delete();
		if ($return_flag) {
			return $result;
		}
		if ($result !== false) {
			$this -> assign('jumpUrl', get_return_url());
			$this -> success("彻底删除{$result}条!");
		} else {
			$this -> error('删除失败!');
		}
	}

	public function del_file($sid) {
		$this -> _destory_file($sid);
	}

	protected function _destory_file($file_list) {
		if (isset($file_list)) {
			if (is_array($file_list)) {
				$files = array_map(think_decrypt, $file_list);
				$where['id'] = array('in', $files);
			} else {
				$files = array_filter(explode(';', $file_list));

				$files = array_map(think_decrypt, $files);

				$where['id'] = array('in', $files);
			}
		} else {
			exit();
		}

		$model = M("File");
		$admin = $this -> config['auth']['admin'];

		if (!$admin) {
			$where['user_id'] = array('eq', get_user_id());
		};

		$list = $model -> where($where) -> select();

		foreach ($list as $file) {

			if (file_exists(__ROOT__ . substr(C('DOWNLOAD_UPLOAD.rootPath'), 2) . $file['savepath'] . $file['savename'])) {
				unlink(__ROOT__ . substr(C('DOWNLOAD_UPLOAD.rootPath'), 2) . $file['savepath'] . $file['savename']);
			}
		}

		$result = $model -> where($where) -> delete();
		if ($result !== false) {
			return true;
		} else {
			return false;
		}
	}

	protected function _chunk_upload() {

		// Make sure file is not cached (as it happens for example on iOS devices)
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		/*
		 // Support CORS
		 header("Access-Control-Allow-Origin: *");
		 // other CORS headers if any...
		 if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		 exit; // finish preflight CORS requests here
		 }
		 */

		// 5 minutes execution time
		@set_time_limit(5 * 60);

		// Uncomment this one to fake upload time
		// usleep(5000);

		// Settings
		$targetDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "plupload";
		//$targetDir = 'uploads';
		$cleanupTargetDir = true;
		// Remove old files
		$maxFileAge = 5 * 3600;
		// Temp file age in seconds

		// Create target dir
		if (!file_exists($targetDir)) {
			@mkdir($targetDir);
		}

		// Get a file name
		if (isset($_REQUEST["name"])) {
			$fileName = $_REQUEST["name"];
		} elseif (!empty($_FILES)) {
			$fileName = $_FILES["file"]["name"];
		} else {
			$fileName = uniqid("file_");
		}

		$filePath = $targetDir . DIRECTORY_SEPARATOR . md5($fileName);

		// Chunking might be enabled
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

		// Remove old temp files
		if ($cleanupTargetDir) {
			if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			}

			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// If temp file is current file proceed to the next
				if ($tmpfilePath == "{$filePath}.part") {
					continue;
				}

				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
					@unlink($tmpfilePath);
				}
			}
			closedir($dir);
		}

		// Open temp file
		if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

		if (!empty($_FILES)) {
			if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}

			// Read binary input stream and append it to temp file
			if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		} else {
			if (!$in = @fopen("php://input", "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		}

		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}

		@fclose($out);
		@fclose($in);

		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
			$files['file']['name'] = $fileName;
			$files['file']['tmp_name'] = $filePath;
			$files['file']['size'] = filesize($filePath);
			$files['file']['is_move'] = true;
			return $files;
		}
	}

	protected function _upload() {
		if (C('CHUNK_UPLOAD')) {
			$files = $this -> _chunk_upload();
		} else {
			$files = $_FILES;
		}

		$return = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File -> upload($files, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG"));

		/* 记录附件信息 */
		if ($info) {
			if (!empty($info['file'])) {
				$return = $info['file'];
			}
			if (!empty($info['imgFile'])) {
				$return = $info['imgFile'];
				$return['url'] = $return['path'];
			}
			$return['sid'] = think_encrypt($info['file']['id']);
			$return['status'] = 1;
			$return['error'] = 0;
		} else {
			$return['status'] = 0;
			$return['info'] = $File -> getError();
		}
		/* 返回JSON数据 */
		$this -> ajaxReturn($return);
	}

	protected function _grab_img($pic_list) {
		$pic_list = explode("|", $pic_list);
		$path = C('EDITOR_UPLOAD.rootPath');
		$return = "";

		foreach ($pic_list as $val) {

			$file_name = $path . md5($val);
			//echo $file_name;
			$return .= get_remote_img($val, $file_name) . "|";
		}
		//dump($return);
		$this -> ajaxReturn($return);
	}

	protected function _down($attach_id) {
		$file_id = think_decrypt($attach_id);
		$File = D('File');
		$root = C('DOWNLOAD_UPLOAD.rootPath');
		if (false === $File -> download($root, $file_id)) {
			$this -> error = $File -> getError();
		}
	}

	//生成查询条件
	protected function _search($model = null) {
		$map = array();
		//过滤非查询条件
		$request = array_filter(array_keys(array_filter($_REQUEST)), "filter_search_field");
		if (empty($model)) {
			$model = D(CONTROLLER_NAME);
		}
		$fields = get_model_fields($model);
		foreach ($request as $val) {
			$field = substr($val, 3);
			$prefix = substr($val, 0, 3);
			if (in_array($field, $fields)) {
				if ($prefix == "be_") {
					if (isset($_REQUEST["en_" . $field])) {
						if (strpos($field, "time") != false) {
							$start_time = date_to_int(trim($_REQUEST[$val]));
							$end_time = date_to_int(trim($_REQUEST["en_" . $field])) + 86400;
							$map[$field] = array( array('egt', $start_time), array('elt', $end_time));
						}
						if (strpos($field, "date") != false) {
							$start_date = trim($_REQUEST[$val]);
							$end_date = trim($_REQUEST["en_" . substr($val, 3)]);
							$map[$field] = array( array('egt', $start_date), array('elt', $end_date));
						}
					}
				}

				if ($prefix == "li_") {
					$map[$field] = array('like', '%' . trim($_REQUEST[$val]) . '%');
				}
				if ($prefix == "eq_") {
					$map[$field] = array('eq', trim($_REQUEST[$val]));
				}
				if ($prefix == "gt_") {
					$map[$field] = array('egt', trim($_REQUEST[$val]));
				}
				if ($prefix == "lt_") {
					$map[$field] = array('elt', trim($_REQUEST[$val]));
				}
			}
		}
		return $map;
	}

	protected function _list($model, $map, $sort = '') {
		//排序字段 默认为主键名
		if (isset($_REQUEST['_sort'])) {
			$sort = $_REQUEST['_sort'];
		} else if (in_array('sort', get_model_fields($model))) {
			$sort = "sort asc";
		} else if (empty($sort)) {
			$sort = "id desc";
		}

		//取得满足条件的记录数
		$count_model = clone $model;
		//取得满足条件的记录数
		$count = $count_model -> where($map) -> count();

		if ($count > 0) {
			//创建分页对象
			if (!empty($_REQUEST['list_rows'])) {
				$list_rows = $_REQUEST['list_rows'];
			} else {
				$list_rows = get_user_config('list_rows');
			}
			import("@.ORG.Util.Page");
			$p = new \Page($count, $list_rows);
			//分页查询数据
			$vo_list = $model -> where($map) -> order($sort) -> limit($p -> firstRow . ',' . $p -> listRows) -> select();

			//echo $model->getlastSql();
			$p -> parameter = $this -> _search($model);
			//分页显示
			$page = $p -> show();
			if ($vo_list) {
				$this -> assign('list', $vo_list);
				$this -> assign('sort', $sort);
				$this -> assign("page", $page);
				return $vo_list;
			}
		}
		return FALSE;
	}

	protected function _assign_folder_list() {
		if ($this -> config['app_type'] == 'personal') {
			$model = D("UserFolder");
		} else {
			$model = D("SystemFolder");
		}
		$list = $model -> get_folder_list();
		$tree = list_to_tree($list);
		$this -> assign('folder_list', dropdown_menu($tree));
	}

	protected function _set_field($id, $field, $val, $name = CONTROLLER_NAME) {
		$model = M($name);
		if (!empty($model)) {
			if (isset($id)) {
				if (is_array($id)) {
					$where['id'] = array("in", array_filter($id));
				} else {
					$where['id'] = array('in', array_filter(explode(',', $id)));
				}

				$admin = $this -> config['auth']['admin'];
				if (in_array('user_id', $model -> getDbFields()) && !$admin) {
					$where['user_id'] = array('eq', get_user_id());
				};
				$list = $model -> where($where) -> setField($field, $val);
				if ($list !== false) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	protected function _user_folder_manage($folder_name, $has_pid = false) {
		$this -> assign('folder_name', $folder_name);
		$this -> assign('has_pid', $has_pid);
		R('UserFolder/index');
	}

	protected function _system_folder_manage($folder_name, $has_pid = false) {
		$this -> assign('folder_name', $folder_name);
		$this -> assign('has_pid', $has_pid);
		R('SystemFolder/index');
	}

	protected function _user_tag_manage($tag_name, $has_pid = false) {
		$this -> assign('tag_name', $tag_name);
		$this -> assign('has_pid', $has_pid);
		R('UserTag/index');
	}

	protected function _system_tag_manage($tag_name, $has_pid = false) {
		$this -> assign('tag_name', $tag_name);
		$this -> assign('has_pid', $has_pid);
		R('SystemTag/index');
	}

	protected function _field_manage($row_type) {
		R('UdfField/index', array('row_type' => $row_type));
	}


}
?>