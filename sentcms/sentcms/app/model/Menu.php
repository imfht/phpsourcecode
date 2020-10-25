<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\model;

/**
 * 菜单模型类
 * @author molong <molong@tensent.cn>
 */
class Menu extends \think\Model {

	protected $type = array(
		'id' => 'integer',
	);

	protected function getIsDevTextAttr($value, $data){
		$is_dev = [0 => '否', 1 => '是'];
		return isset($is_dev[$data['is_dev']]) ? $is_dev[$data['is_dev']] : '是';
	}

	protected function getHideTextAttr($value, $data){
		$is_dev = [0 => '否', 1 => '是'];
		return isset($is_dev[$data['hide']]) ? $is_dev[$data['hide']] : '是';
	}

	public function getAuthNodes($type = 'admin') {
		$map['type'] = $type;
		$rows = $this->field('id,pid,group,title,url')->where($map)->order('id asc')->select();
		foreach ($rows as $key => $value) {
			$data[$value['id']] = $value;
		}
		foreach ($data as $key => $value) {
			if ($value['pid'] > 0) {
				$value['group'] = $data[$value['pid']]['title'] . '管理';
				$list[] = $value;
			}
		}
		return $list;
	}
}