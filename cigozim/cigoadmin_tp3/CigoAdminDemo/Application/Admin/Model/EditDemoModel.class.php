<?php

namespace Admin\Model;

use CigoAdminLib\Lib\Admin;
use Admin\Lib\AdminPage;
use Think\Model;

class EditDemoModel extends Model {
	protected $_validate = array(
		array('title', 'require', '标题不能为空!', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('sort', 'number', '排序必须为数字！', self::VALUE_VALIDATE, '', self::MODEL_BOTH)
	);

	protected $_auto = array(
		array('status', 0, self::MODEL_BOTH, 'string')
	);

	public function getList($map = array('status' => array('gt', -1)), $orderBy = 'sort desc, create_time desc') {
		$page = new AdminPage($this->where($map)->count(), Admin::DATA_LIST_SIZE);
		$data_list = $this->where($map)->limit($page->firstRow, $page->listRows)
			->order($orderBy)->select();
		if ($data_list) {
			return array(
				'showPage' => $page->show(),
				'dataList' => $data_list
			);
		} else {
			return false;
		}
	}
}
