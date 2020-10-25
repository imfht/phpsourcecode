<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class SettingController extends AdminbaseController {

	protected $options_obj;

	public function _initialize() {
		parent::_initialize();
		$this->options_obj = M('Options');
	}

	function index() {
		
	}

	/**
	 * 前台网站信息配置
	 */
	function site() {
		if (IS_POST) {
			$where = array();
			if (isset($_POST['option_id'])) {
				$where = array("eq" => $_POST['option_id']);
			}

			$home_configs = (array) F("site_options", "", C('CMF_CONF_PATH'));
			$home_configs["DEFAULT_THEME"] = $_POST['options']['site_tpl'];
			$home_configs["URL_MODEL"] = $_POST['options']['urlmode'];
			$home_configs["URL_HTML_SUFFIX"] = $_POST['options']['html_suffix'];
//			$home_configs['TMPL_ACTION_ERROR'] = C("CMF_TPL_PATH") . $_POST['options']['site_tpl'] . '/error.html'; // 默认错误跳转对应的模板文件
//			$home_configs['TMPL_ACTION_SUCCESS'] = C("CMF_TPL_PATH") . $_POST['options']['site_tpl'] . '/success.html'; // 默认成功跳转对应的模板文件
//			$home_configs['TMPL_EXCEPTION_FILE'] = C("CMF_TPL_PATH") . $_POST['options']['site_tpl'] . '/error.html'; // 异常页面的模板文件
			$home_configs['TMPL_PARSE_STRING'] = array(
				'__TMPL__'	 => __ROOT__ . '/static/Portal/' . $_POST['options']['site_tpl'],
				'__STATIC__' => __ROOT__ . '/static',
			);
			$data['option_name'] = "site_options";
			$data['option_value'] = json_encode($_POST['options']);
			$r = $this->options_obj->where($where)->add($data, array(), true);
			if ($r) {
				F("site_options", array_merge($home_configs, get_site_options()), C('CMF_CONF_PATH'));
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		} else {
			$option = $this->options_obj->where("option_name='site_options'")->find();
			$noneed = array(".", "..", ".svn");
			$tpls = array_diff(scandir(C("CMF_TPL_PATH")), $noneed);
			$this->assign("templates", $tpls);
			if ($option) {
				$this->assign((array) json_decode($option['option_value']));
				$this->assign("option_id", $option['option_id']);
			}
			$this->display();
		}
	}

	/**
	 * 用户密码修改
	 */
	function password() {
		if (IS_POST) {
			$user_obj = new \Admin\Model\UsersModel();
			$admin = session('user');
			$old_password = md5($_POST['old_password']);
			$password = md5($_POST['password']);
			if ($old_password == $admin['user_pass']) {
				if ($admin['user_pass'] == $password) {
					$this->error("新密码不能和原始密码相同！");
				} else {
					$data = array();
					$data['user_pass'] = $password;
					$data['ID'] = $admin['ID'];
					$r = $user_obj->save($data);
					if ($r) {
						$admin['user_pass'] = $password;
						session('user', $admin);
						$this->success("修改成功！");
					} else {
						$this->error("修改失败！");
					}
				}
			} else {
				$this->error("原始密码不正确！");
			}
		} else {
			$this->display();
		}
	}

	//清除缓存
	function clearcache() {
		clear_cache();
		$this->display();
	}

}
