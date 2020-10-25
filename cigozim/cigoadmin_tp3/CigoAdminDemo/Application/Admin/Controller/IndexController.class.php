<?php

namespace Admin\Controller;

use CigoAdminLib\Lib\SessionCheck;

class IndexController extends SessionCheck {
	public function index() {
		$this->assign('title', 'CigoAdmin');
		$this->assign('admin_module_name', '后台');
		$this->display('index');
	}

	public function getLeftMenuData() {
		$model = D('MenuAdmin');
		$dataList = $model->getList(array('status' => array('eq', 1)));
		$treeList = array();
		$model->convertToTree($dataList, $treeList, 0, 'pid');

		$this->success($treeList, '', true);
	}

	public function getTopMenuData() {
		$model = D('MenuAdmin');
		$dataList = $model->getTopList(
			array(
				'status' => array('eq', 1),
				'show_top_menu' => array('eq', 1)
			)
		);
		$treeList = array();
		$model->convertToTree($dataList, $treeList, 0, 'pid', false);
		$topList = array();
		$model->getTopTree($treeList, $topList);

		$this->success($topList, '', true);
	}

	public function addOptRate() {
		$model = D('MenuAdmin');
		$model->where(array('id' => array('in', $_POST['ids'])))->setInc('opt_rate', 1, 15);
	}
}