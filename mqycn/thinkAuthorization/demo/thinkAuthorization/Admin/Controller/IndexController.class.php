<?php
namespace Admin\Controller;

class IndexController extends _AdminController {
	public function index() {

		$tpl_path = "Admin/View/Authorization/";
		$tpl_src = APP_PATH . "../../src/" . $tpl_path;

		//运行之前，将模板复制到权限管理目录
		if (!is_dir(APP_PATH . $tpl_path)) {
			mkdir(APP_PATH . $tpl_path);
		}
		foreach (scandir($tpl_src) as $file) {
			if (is_file($tpl_src . $file)) {
				copy($tpl_src . $file, APP_PATH . $tpl_path . $file);
			}
		}

		$this->error("已经将模板文件复制到测试目录", U("/Admin/Authorization"));
	}

	public function index2() {
		echo '<a href="index">返回首页</a>';
	}

	public function index3() {
		echo '<a href="index">返回首页</a>';
	}
}