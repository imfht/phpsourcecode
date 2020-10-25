<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\Addons as AddonsM;
use app\model\Hooks;
use think\facade\Cache;
use think\facade\Config;

/**
 * @title 插件管理
 * @description 插件管理
 */
class Addons extends Base {

	public function initialize() {
		parent::initialize();
		$this->getAddonsMenu();
	}

	/**
	 * @title 插件列表
	 */
	public function index($refresh = 0) {
		$map = [];
		if ($refresh) {
			AddonsM::refreshAddons($this->app->getRootPath() . 'addons' . DIRECTORY_SEPARATOR);
		}

		$list = AddonsM::where($map)->order('id desc')->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 删除插件
	 */
	public function del() {
	}

	/**
	 * @title 安装插件
	 */
	public function install() {
		$addon_name = input('addon_name', '', 'trim');
		$class = get_addons_class($addon_name);
		if (class_exists($class)) {
			$addons = get_addons_instance($addon_name);
			$info = $addons->getInfo();
			session('addons_install_error', null);
			$install_flag = $addons->install();
			if (!$install_flag) {
				return $this->error('执行插件预安装操作失败' . session('addons_install_error'));
			}
			$result = AddonsM::install($info);
			if ($result) {
				Cache::delete("sentcms_hooks");
				return $this->success('安装成功');
			} else {
				return $this->error("安装失败！");
			}
		} else {
			return $this->error('插件不存在');
		}
	}

	/**
	 * @title 卸载插件
	 */
	public function uninstall($id) {
		$result = AddonsM::uninstall($id);
		if ($result === false) {
			Cache::delete("sentcms_hooks");
			return $this->error($this->addons->getError(), '');
		} else {
			return $this->success('卸载成功！');
		}
	}

	/**
	 * @title 启用插件
	 */
	public function enable($id) {
		$result = AddonsM::update(['status' => 1], ['id' => $id]);
		if ($result) {
			Cache::delete('sentcms_hooks');
			return $this->success('启用成功');
		} else {
			return $this->error("启用失败！");
		}
	}

	/**
	 * @title 禁用插件
	 */
	public function disable($id) {
		$result = AddonsM::update(['status' => 0], ['id' => $id]);
		if ($result) {
			Cache::delete('sentcms_hooks');
			return $this->success('禁用成功');
		} else {
			return $this->error("禁用失败！");
		}
	}

	/**
	 * @title 设置插件页面
	 */
	public function config() {
		if ($this->request->isPost()) {
			$config = $this->request->post();
			$id = $this->request->param('id');

			$result = AddonsM::update(['config' => $config], ['id' => $id]);
			if ($result) {
				return $this->success('完成设置！');
			} else {
				return $this->error("无法完成设置！");
			}
		} else {
			$id = $this->request->param('id');
			if (!$id) {
				return $this->error("非法操作！");
			}
			$info = AddonsM::find($id);
			if (!empty($info)) {
				$class = get_addons_instance($info['name']);

				$keyList = $class->getConfig(true);
				$this->data = array(
					'info' => $info['config'],
					'keyList' => $keyList,
					'meta_title' => $info['title'] . " - 设置",
				);
				return $this->fetch('admin/public/edit');
			} else {
				return $this->error("未安装此插件！");
			}
		}
	}

	/**
	 * @title 检测插件
	 * 获取插件所需的钩子是否存在，没有则新增
	 * @param string $str  钩子名称
	 * @param string $addons  插件名称
	 * @param string $addons  插件简介
	 */
	public function existHook($str, $addons, $msg = '') {
		$hook_mod = db('Hooks');
		$where['name'] = $str;
		$gethook = $hook_mod->where($where)->find();
		if (!$gethook || empty($gethook) || !is_array($gethook)) {
			$data['name'] = $str;
			$data['description'] = $msg;
			$data['type'] = 1;
			$data['update_time'] = time();
			$data['addons'] = $addons;
			if (false !== $hook_mod->create($data)) {
				$hook_mod->add();
			}
		}
	}

	/**
	 * @title 删除钩子
	 * @param string $hook  钩子名称
	 */
	public function deleteHook($hook) {
		$model = db('hooks');
		$condition = array(
			'name' => $hook,
		);
		$model->where($condition)->delete();
		S('hooks', null);
	}

	/**
	 * @title 钩子列表
	 */
	public function hooks() {
		$map = [];
		$order = "id desc";

		$list = Hooks::where($map)->order($order)->paginate($this->request->pageConfig)->append(['type_text']);

		$this->data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 添加钩子
	 */
	public function addhook() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$data['addons'] = isset($data['addons'][0]) ? $data['addons'][0] : [];

			$result = Hooks::create($data);
			if ($result !== false) {
				return $this->success("添加成功");
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$keylist = Hooks::getaddons([]);
			$this->data = array(
				'keyList' => $keylist,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑钩子
	 */
	public function edithook($id) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$data['addons'] = isset($data['addons'][0]) ? $data['addons'][0] : [];

			$result = Hooks::update($data, ['id' => $data['id']]);
			if ($result !== false) {
				return $this->success("修改成功");
			} else {
				return $this->error('修改失败！');
			}
		} else {
			$info = Hooks::find($id);
			$keylist = Hooks::getaddons($info);
			$this->data = array(
				'info' => $info,
				'keyList' => $keylist,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除钩子
	 */
	public function delhook() {
		$id = $this->getArrayParam('id');
		$map['id'] = array('IN', $id);
		$result = $this->hooks->where($map)->delete();
		if ($result !== false) {
			return $this->success('删除成功');
		} else {
			return $this->error('删除失败');
		}
	}

	/**
	 * @title 更新钩子
	 */
	public function updateHook() {
		$hookModel = D('Hooks');
		$data = $hookModel->create();
		if ($data) {
			if ($data['id']) {
				$flag = $hookModel->save($data);
				if ($flag !== false) {
					S('hooks', null);
					$this->success('更新成功', Cookie('__forward__'));
				} else {
					$this->error('更新失败');
				}
			} else {
				$flag = $hookModel->add($data);
				if ($flag) {
					S('hooks', null);
					$this->success('新增成功', Cookie('__forward__'));
				} else {
					$this->error('新增失败');
				}
			}
		} else {
			$this->error($hookModel->getError());
		}
	}
}