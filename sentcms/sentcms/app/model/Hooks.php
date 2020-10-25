<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\model;

/**
 * 分类模型
 */
class Hooks extends \think\Model {

	protected function getTypeTextAttr($value, $data){
		$type = [1 => '视图', 2 => '控制器'];
		return isset($type[$data['type']]) ? $type[$data['type']] : '';
	}

	protected function setAddonsAttr($value){
		if (is_array($value) && !empty($value)) {
			return implode(",", $value);
		}
	}

	public static function getaddons($addons){
		if (isset($addons['addons']) && $addons['addons']) {
			$hook_list = explode(',', $addons['addons']);
			foreach ($hook_list as $key => $value) {
				$field_list[] = array('id' => $value, 'title' => $value, 'name' => $value, 'is_show' => 1);
			}
			$option[] = ['name' => '钩子挂载排序', 'list' => $field_list];
		} else {
			$option[] = ['name' => '钩子挂载排序', 'list' => []];
		}
		$keylist = [
			['name' => 'name', 'title' => '钩子名称', 'type' => 'text', 'help' => '需要在程序中先添加钩子，否则无效'],
			['name' => 'description', 'title' => '钩子描述', 'type' => 'text', 'help' => '钩子的描述信息'],
			['name' => 'type_text', 'title' => '钩子类型', 'type' => 'select', 'option' => [['key'=>1, 'label'=>'视图'], ['key' => 2, 'label' => '控制器']], 'help' => '钩子的描述信息'],
			['name' => 'addons', 'title' => '插件排序', 'type' => 'kanban', 'option' => $option],
		];
		return $keylist;
	}

	public static function addHooks($name){
		// 读取插件目录及钩子列表
		$base = get_class_methods("\\sent\\Addons");
		// 读取出所有公共方法
		$methods = (array)get_class_methods("\\addons\\" . $name . "\\Plugin");
		// 跟插件基类方法做比对，得到差异结果
		$hooks = array_diff($methods, $base);
		$row = self::where('name', 'IN', $hooks)->column("*", "name");
		$save = [];
		foreach ($hooks as $value) {
			if (isset($row[$value]) && !empty($row[$value])) {
				$info = $row[$value];
				$addons = $info['addons'] ? explode(",", $info['addons']) : [];
				$info['addons'] = empty($addons) ? [$name] : array_push($addons, $name);
				$save[] = $info;
			}else{
				$save[] = ['name' => $value, 'type' => 1, 'create_time' => time(), 'status' => 1, 'addons'=> $name];
			}
		}
		return (new self())->saveAll($save);
	}

	public static function removeHooks($name){
		$row = self::where('addons', 'LIKE', '%'.$name.'%')->select()->toArray();
		$save = [];
		foreach ($row as $value) {
			$addons = explode(",", $value['addons']);
			if (in_array($name, $addons)) {
				array_splice($addons, array_search($name, $addons), 1);
			}
			$value['addons'] = !empty($addons) ? implode(",", $addons) : "";
			$save[] = $value;
		}
		if (!empty($save)) {
			return (new self())->saveAll($save);
		}else{
			return true;
		}
	}
}