<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 友情链接类
 * @author molong <molong@tensent.cn>
 */
class Hooks extends Base {

	public $keyList = array(
		array('name' => 'name', 'title' => '钩子名称', 'type' => 'text', 'help' => '需要在程序中先添加钩子，否则无效'),
		array('name' => 'description', 'title' => '钩子描述', 'type' => 'text', 'help' => '钩子的描述信息'),
		array('name' => 'type_text', 'title' => '钩子类型', 'type' => 'select', 'help' => '钩子的描述信息'),
		array('name' => 'addons', 'title' => '插件排序', 'type' => 'kanban'),
	);

	public function initialize() {
		parent::initialize();
		foreach ($this->keyList as $key => $value) {
			if ($value['name'] == 'type_text') {
				$value['option'] = \think\Config::get('hooks_type');
			}
			$this->keyList[$key] = $value;
		}
	}

	protected function setAddonsAttr($value) {
		if ($value) {
			$string = implode(",", $value);
			return $string;
		}
	}

	protected function getTypeTextAttr($value, $data) {
		$hooks_type = config('hooks_type');
		return $hooks_type[$data['type']];
	}

	/**
	 * 处理钩子挂载插件排序
	 */
	public function getaddons($addons = '') {
		if ($addons) {
			$hook_list = explode(',', $addons);
			foreach ($hook_list as $key => $value) {
				$field_list[] = array('id' => $value, 'title' => $value, 'name' => $value, 'is_show' => 1);
			}
			$option[1] = array('name' => '钩子挂载排序', 'list' => $field_list);
		} else {
			$option[] = array('name' => '钩子挂载排序', 'list' => array());
		}
		foreach ($this->keyList as $key => $value) {
			if ($value['name'] == 'addons') {
				$value['option'] = $option;
			}
			$keylist[] = $value;
		}
		return $keylist;
	}

	public function addHooks($addons_name) {
		$addons_class = get_addon_class($addons_name); //获取插件名
		if (!class_exists($addons_class)) {
			$this->error = "未实现{$addons_name}插件的入口文件";
			return false;
		}
		$methods = array_diff(get_class_methods($addons_class), get_class_methods('\app\common\controller\Addons'));
		$methods = array_diff($methods, array('install', 'uninstall'));
		foreach ($methods as $item) {
			$info = $this->where('name', $item)->find();
			if (null == $info) {
				$save = array(
					'name'        => $item,
					'description' => '',
					'type'        => 1,
					'addons'      => array($addons_name),
					'update_time' => time(),
					'status'      => 1,
				);
				self::create($save);
			} else {
				if ($info['addons']) {
					$addons = explode(',', $info['addons']);
					array_push($addons, $addons_name);
				} else {
					$addons = array($addons_name);
				}
				$this->where('name', $item)->setField('addons', implode(',', $addons));
			}
		}
		return true;
	}

	public function removeHooks($addons_name) {
		$addons_class = get_addon_class($addons_name); //获取插件名
		if (!class_exists($addons_class)) {
			$this->error = "未实现{$addons_name}插件的入口文件";
			return false;
		}
		$row = $this->where(array('addons' => array('like', '%' . $addons_name . '%')))->select();
		foreach ($row as $value) {
			if ($addons_name === $value['addons']) {
				$this->where('id', $value['id'])->delete();
			} else {
				$addons = explode(',', $value['addons']);
				$key    = array_search($addons_name, $addons);
				if ($key) {
					unset($addons[$key]);
					$addons = implode(',', $addons);
					$this->where('id', $value['id'])->setField('addons', $addons);
				}
			}
		}
		return true;
	}
}