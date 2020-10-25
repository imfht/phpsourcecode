<?php

namespace Admin\Model;

use CigoAdminLib\Model\TreeModel;

class MenuAdminModel extends TreeModel {
	protected $_validate = array(
		array('title', 'require', '标题不能为空!', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('pid', 'require', '父级编号不能为空!', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('sort', 'number', '排序必须为数字！', self::VALUE_VALIDATE, '', self::MODEL_BOTH)
	);

	protected $_auto = array(
		array('status', 0, self::MODEL_INSERT, 'string'),
		array('status', 1, self::MODEL_BOTH, 'string'),
	);

	public function getList($map = array('status' => array('gt', -1))) {
		$data_list = $this->where($map)->order('`pid` asc, `group_sort` desc, `group` asc, `sort` desc, `id` asc')->select();
		if ($data_list) {
			foreach ($data_list as $key => $item) {
				$data_list[$key]['url'] = empty($item['url']) ? '' : get_menu_url($item['url']);
			}
			return $data_list;
		} else {
			return false;
		}
	}

	public function getTopList($map = array('status' => array('gt', -1))) {
		$data_list = $this->where($map)->order('`pid` asc, `opt_rate` desc, `id` asc')->select();
		if ($data_list) {
			foreach ($data_list as $key => $item) {
				$data_list[$key]['url'] = empty($item['url']) ? '' : get_menu_url($item['url']);
			}
			return $data_list;
		} else {
			return false;
		}
	}
}
