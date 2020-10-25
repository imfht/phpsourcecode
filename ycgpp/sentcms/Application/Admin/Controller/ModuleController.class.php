<?php 
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;

class ModuleController extends \Common\Controller\AdminController{
	
	protected $moduleModel;

	function _initialize(){
		$this->model = D('Module');
		parent::_initialize();
		$app = include(CONF_PATH.'app.php');
	}

	public function lists(){
		$this->setMeta('应用管理');
		$listBuilder = new \OT\Builder();
		/*刷新模块列表时清空缓存*/
		$aRefresh = I('get.refresh', 0, 'intval');
		if ($aRefresh) {
			S('module_all', null);
		}
		/*刷新模块列表时清空缓存 end*/
		$modules = $this->model->getAll();
		foreach ($modules as &$m) {
			$m['alias'] = '<i class="fa fa-' . $m['icon'] . '"></i> ' . $m['alias'];
			if ($m['is_com']) {
				$m['is_com'] = '<strong style="color: orange">商业模块</strong>';
			} else {
				$m['is_com'] = '<strong style="color: green">免费模块</strong>';
			}
		}
		$this->assign('modules',$modules);
		$this->display();
	}
	
	public function uninstall(){
		$aId = I('get.id', 0, 'intval');
		$res = $this->model->uninstall($aId);
		if ($res === true) {
			$this->success('卸载模块成功。', U('lists'));
		} else {
			$this->error('卸载模块失败。' . $res['error_code']);
		}
	}

	public function install(){
		$aId = I('get.id', 0, 'intval');
		$res = $this->model->install($aId);
		if ($res === true) {
			$this->success('安装模块成功。');
		} else {
			$this->error('安装模块失败。' . $res['error_code']);
		}
	}
} 
