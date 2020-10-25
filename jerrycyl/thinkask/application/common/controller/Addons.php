<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */

namespace app\common\controller;

/**
 * 插件类
 * @author yangweijie <yangweijiester@gmail.com>
 */
class Addons extends Base {

	public $info             = array();
	public $addon_path       = '';
	public $config_file      = '';
	public $custom_config    = '';
	public $admin_list       = array();
	public $custom_adminlist = '';
	public $access_url       = array();

	public function _initialize() {
		$mc = $this->getAddonsName();

		$this->addon_path = ROOT_PATH . "/addons/{$mc}/";
		if (is_file($this->addon_path . 'config.php')) {
			$this->config_file = $this->addon_path . 'config.php';
		}
	}

	public function template($template) {
		$mc                         = $this->getAddonsName();
		$ac                         = input('ac', '', 'trim,strtolower');
		$parse_str                  = \think\Config::get('parse_str');
		$parse_str['__ADDONROOT__'] = ROOT_PATH . "/addons/{$mc}";
		\think\Config::set('parse_str', $parse_str);

		if ($template) {
			$template = $template;
		} else {
			$template = $mc . "/" . $ac;
		}

		$this->view->engine(
			array('view_path' => "addons/" . $mc . "/view/")
		);
		echo $this->fetch($template);
	}

	final public function getAddonsName() {
		$mc = input('mc', '', 'trim,strtolower');
		if ($mc) {
			return $mc;
		} else {
			$class = get_class($this);
			return strtolower(substr($class, strrpos($class, '\\') + 1));
		}
	}

	final public function checkInfo() {
		$info_check_keys = array('name', 'title', 'description', 'status', 'author', 'version');
		foreach ($info_check_keys as $value) {
			if (!array_key_exists($value, $this->info)) {
				return false;
			}

		}
		return true;
	}

	public function getConfig() {

		static $_config = array();
		if (empty($name)) {
			$name = $this->getAddonsName();
		}
		if (isset($_config[$name])) {
			return $_config[$name];
		}
		$config        = array();
		$map['name']   = $name;
		$map['status'] = 1;
		$config        = db('Addons')->where($map)->value('config');
		if ($config) {
			$config = json_decode($config, true);
		} else {
			$config = array();
			$temp_arr = include $this->config_file;
			foreach ($temp_arr as $key => $value) {
				if ($value['type'] == 'group') {
					foreach ($value['options'] as $gkey => $gvalue) {
						foreach ($gvalue['options'] as $ikey => $ivalue) {
							$config[$ikey] = $ivalue['value'];
						}
					}
				} else {
					$config[$key] = $value['value'];
				}
			}
		}
		$_config[$name] = $config;
		return $config;
	}

	/**
	 * 获取插件所需的钩子是否存在，没有则新增
	 * @param string $str  钩子名称
	 * @param string $addons  插件名称
	 * @param string $addons  插件简介
	 */
	public function getisHook($str, $addons, $msg = '') {
		$hook_mod      = db('Hooks');
		$where['name'] = $str;
		$gethook       = $hook_mod->where($where)->find();
		if (!$gethook || empty($gethook) || !is_array($gethook)) {
			$data['name']        = $str;
			$data['description'] = $msg;
			$data['type']        = 1;
			$data['update_time'] = time();
			$data['addons']      = $addons;
			if (false !== $hook_mod->create($data)) {
				$hook_mod->add();
			}
		}
	}

	/**
	 * 删除钩子
	 * @param string $hook  钩子名称
	 */
	public function deleteHook($hook) {
		$model     = db('hooks');
		$condition = array(
			'name' => $hook,
		);
		$model->where($condition)->delete();
	}
}