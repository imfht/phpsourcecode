<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace addons\sitestat;

use \think\facade\Db;

/**
 * 系统环境信息插件
 * @author thinkphp
 */

class Plugin extends \sent\Addons {

	public $info = array(
		'name' => 'Sitestat',
		'title' => '站点统计信息',
		'description' => '统计站点的基础信息',
		'status' => 1,
		'author' => 'molong',
		'version' => '0.2',
	);

	public function install() {
		return true;
	}

	public function uninstall() {
		return true;
	}

	//实现的AdminIndex钩子方法
	public function AdminIndex($param) {
		$config = $this->getConfig();
		$this->assign('addons_config', $config);
		if ($config['display']) {
			$info['users'] = Db::name('Member')->count();
			$info['form'] = Db::name('Form')->count();
			$info['category'] = Db::name('Category')->count();
			$info['model'] = Db::name('Model')->count();
			$this->assign('info', $info);
			return $this->fetch('index/info');
		}
	}
}