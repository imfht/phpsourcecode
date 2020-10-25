<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace addons\syslogin;

/**
 * 系统环境信息插件
 * @author thinkphp
 */

class Plugin extends \sent\Addons {

	public $info = array(
		'name' => 'Syslogin',
		'title' => '第三方登录',
		'description' => '第三方登录',
		'status' => 1,
		'author' => 'molong',
		'version' => '0.1',
	);

	public function loginBottomAddon() {
		return $this->fetch('login');
	}

	public function install() {
		return true;
	}

	public function uninstall() {
		return true;
	}
}