<?php

namespace Admin\Controller;

use CigoAdminLib\Lib\Admin;
use CigoAdminLib\Lib\AdminDataMg;
use Think\Model;

class AdminMenuController extends AdminDataMg {
	public function index() {
		$this->assign('label_title', '后台菜单');
		$this->display();
	}

	public function getMenuTreeData() {
		$model = D('MenuAdmin');
		$dataList = $model->getList();
		$treeList = array();
		$model->convertToTree($dataList, $treeList, 0, 'pid', false);

		$this->success($treeList, '', true);
	}

	public function setStatus() {
		$this->doSetStatus(M('MenuAdmin'), Admin::DATA_TYPE_MENU_ADMIN);
	}

	public function add() {
		$this->doAdd(D('MenuAdmin'));
	}

	private function getParentMenuList() {
		$model = D('MenuAdmin');
		$dataList = $model->field('id, title text, path path')
			->where(array('status' => array('gt', -1)))->order('path asc, sort desc, id asc')->select();
		$this->assign('parent_menu_list', json_encode($dataList ? $dataList : array()));
	}

	protected function beforeAddDisplay($model) {
		$this->assign('label_title', '添加菜单');
		$this->assign('pid', I('get.pid') ? I('get.pid') : '0');
		$this->assign('target_list', json_encode($this->getLinkTargetList()));
		$this->assign('label_class_list', json_encode($this->getLabelClassList()));
		$this->getParentMenuList();
	}

	public function edit() {
		$this->doEdit(D('MenuAdmin'), Admin::DATA_TYPE_MENU_ADMIN);
	}

	protected function beforeEditDisplay($model, &$data) {
		$this->assign('label_title', '编辑菜单');
		$this->assign('target_list', json_encode($this->getLinkTargetList()));
		$this->assign('label_class_list', json_encode($this->getLabelClassList()));
		$this->getParentMenuList();
	}

	public function editValItem() {
		$model = M('MenuAdmin');
		$model->validate(array(
			array('sort', 'number', '排序必须为数字！', Model::VALUE_VALIDATE, '', Model::MODEL_BOTH)
		));
		$this->doEditValItem($model);
	}


    private function getLinkTargetList()
    {
        return array(
            array('id' => 'page_content', 'text' => '右侧内容窗口'),
            array('id' => '_blank', 'text' => '新窗口打开')
        );
    }

    private function getLabelClassList()
    {
        return array(
            array('id' => 'label-default', 'text' => 'Default-样式'),
            array('id' => 'label-primary', 'text' => 'Primary-样式'),
            array('id' => 'label-success', 'text' => 'Success-样式'),
            array('id' => 'label-info', 'text' => 'Info-样式'),
            array('id' => 'label-warning', 'text' => 'Warning-样式'),
            array('id' => 'label-danger', 'text' => 'Danger-样式')
        );
    }
}
