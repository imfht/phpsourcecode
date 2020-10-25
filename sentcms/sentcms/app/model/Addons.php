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
 * 扩展模型
 */
class Addons extends \think\Model {

	protected $auto   = ['status', 'isinstall', 'update_time'];
	protected $insert = ['create_time'];

	protected $type = [
		'hooks' => 'json',
		'config' => 'json'
	];

	protected function setStatusAttr($value) {
		return $value ? $value : 0;
	}

	protected function setNameAttr($value){
		return $value ? strtolower($value) : '';
	}

	protected function setIsinstallAttr($value) {
		return $value ? $value : 0;
	}

	protected function getStatusTextAttr($value, $data) {
		return $data['status'] ? "启用" : "禁用";
	}

	protected function getUninstallAttr($value, $data) {
		return 0;
	}

	/**
	 * 更新插件列表
	 * @param string $addon_dir
	 */
	public static function refreshAddons($addon_dir = '') {
		if (!$addon_dir) {
			$addon_dir = SENT_ADDON_PATH;
		}
		$dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
		if ($dirs === FALSE || !file_exists($addon_dir)) {
			return FALSE;
		}
		$where[] = ['name', 'in', $dirs];
		$addons        = self::where($where)->column('*', 'name');
		
		$save = [];
		foreach ($dirs as $value) {
			$value = strtolower($value);
			$class = "\\addons\\" . $value . "\\Plugin";
			if (!class_exists($class)) {
				continue;
			}
			$item = get_addons_info($value);
			if (isset($addons[$value])) {
				$item['id'] = $addons[$value]['id'];
				unset($item['status']);
			}
			$save[] = $item;
		}
		$class = new self();
		return $class->saveAll($save);
	}

	/**
	 * 获取插件的后台列表
	 */
	public function getAdminList() {
		$admin     = [];
		$map[] = ['status', '=', 1];
		$map[] = ['has_adminlist', '=', 1];
		$db_addons = self::where($map)->field('title,name')->select();
		if ($db_addons) {
			foreach ($db_addons as $value) {
				$admin[] = array('title' => $value['title'], 'url' => "Addons/adminList?name={$value['name']}");
			}
		}
		return $admin;
	}

	public static function install($data) {
		if ($data) {
			$id = self::where('name', strtolower($data['name']))->value('id');
			$result = false;
			if ($id) {
				$result = self::update(['isinstall'=>1, 'status'=>1], ['id'=>$id]);
			}
			if (false !== $result) {
				return Hooks::addHooks(strtolower($data['name']));
			}else{
				return false;
			}
		} else {
			return false;
		}
	}

	public static function uninstall($id) {
		$info = self::find($id);
		if (!$info) {
			return false;
		}
		$class = get_addons_class($info['name']);
		if (class_exists($class)) {
			//插件卸载方法
			$addons = get_addons_instance($info['name']);
			if (!method_exists($addons, 'uninstall')) {
				return false;
			}
			$result = $addons->uninstall();
			if ($result) {
				//卸载挂载点中的插件
				$result = Hooks::removeHooks($info['name']);
				//删除插件表中数据
				$info->save(['isinstall' => 0]);
				return true;
			} else {
				return false;
			}
		}
	}

	public function build() {

	}
}