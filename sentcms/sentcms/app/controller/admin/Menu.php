<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use sent\tree\Tree;
use app\model\Menu as MenuM;
use think\facade\Cache;

/**
 * @title 菜单管理
 */
class Menu extends Base {

	public function _initialize() {
		parent::_initialize();
	}

	/**
	 * @title 菜单列表
	 */
	public function index(MenuM $menu) {
		$map = array();
		$title = trim(input('get.title'));
		$list = $menu->where($map)->field(true)->order('sort asc,id asc')->select();
		// int_to_string($list, array('hide' => array(1 => '是', 0 => '否'), 'is_dev' => array(1 => '是', 0 => '否')));

		if (!empty($list)) {
			$list = (new Tree())->toFormatTree($list->append(['is_dev_text', 'hide_text'])->toArray());
		}
		// 记录当前列表页的cookie
		// Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$this->data['list'] = $list;
		return $this->fetch();
	}

	/**
	 * @title 编辑菜单字段
	 */
	public function editable() {
		$name = $this->request->param('name', '');
		$value = $this->request->param('value', '');
		$pk = $this->request->param('pk', '');

		if ($name && $value && $pk) {
			$save[$name] = $value;
			MenuM::update($save, ['id' => $pk]);
		}
	}

	/**
	 * @title 新增菜单
	 * @author yangweijie <yangweijiester@gmail.com>
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->post();

			$id = MenuM::create($data);
			if ($id) {
				Cache::pull('admin_menu_list');
				return $this->success('新增成功', Cookie('__forward__'));
			} else {
				return $this->error('新增失败');
			}
		} else {
			$menus = MenuM::select()->toArray();

			$tree = new \sent\tree\Tree();
			$menus = $tree->toFormatTree($menus);

			if (!empty($menus)) {
				$menus = array_merge(array(0 => array('id' => 0, 'title_show' => '顶级菜单')), $menus);
			} else {
				$menus = array(0 => array('id' => 0, 'title_show' => '顶级菜单'));
			}
			$this->data = [
				'info'   => ['pid' => $this->request->param('pid', 0)],
				'Menus' => $menus
			];
			return $this->fetch('edit');
		}
	}

	/**
	 * @title 编辑配置
	 * @author yangweijie <yangweijiester@gmail.com>
	 */
	public function edit($id = 0) {
		if ($this->request->isPost()) {
			$data = $this->request->post();

			$result = MenuM::update($data, ['id' => $data['id']]);

			if (false !== $result) {
				return $this->success('更新成功', '/admin/menu/index');
			} else {
				return $this->error('更新失败');
			}
		} else {
			$info = [];
			/* 获取数据 */
			$info = MenuM::find($id);

			$menus = MenuM::select()->toArray();

			$tree = new \sent\tree\Tree();
			$menus = $tree->toFormatTree($menus);

			$menus = array_merge(array(0 => array('id' => 0, 'title_show' => '顶级菜单')), $menus);
			if (false === $info) {
				return $this->error('获取后台菜单信息错误');
			}

			$this->data = [
				'Menus' => $menus,
				'info'  => $info
			];
			return $this->fetch();
		}
	}

	/**
	 * @title 删除菜单
	 * @author yangweijie <yangweijiester@gmail.com>
	 */
	public function del() {
		$id = $this->request->param('id', '');

		$map = [];
		if (!$id) {
			return $this->error('请选择要操作的数据!');
		}
		if (is_array($id)) {
			$map[] = ['id', 'IN', $id];
		}else{
			$map[] = ['id', '=', $id];
		}

		$result = MenuM::where($map)->delete();
		if (false !== $result) {
			Cache::pull('admin_menu_list');
			return $this->success('删除成功');
		} else {
			return $this->error('删除失败！');
		}
	}

	public function toogleHide($id, $value = 1) {
		Cache::pull('admin_menu_list');

		$result = MenuM::update(['hide'=> $value], ['id' => $id]);
		if ($result !== false) {
			return $this->success('操作成功！');
		} else {
			return $this->error('操作失败！');
		}
	}

	public function toogleDev($id, $value = 1) {
		Cache::pull('admin_menu_list');

		$result = MenuM::update(['is_dev'=> $value], ['id' => $id]);
		if ($result !== false) {
			return $this->success('操作成功！');
		} else {
			return $this->error('操作失败！');
		}
	}

	public function importFile($tree = null, $pid = 0) {
		if ($tree == null) {
			$file = APP_PATH . "Admin/Conf/Menu.php";
			$tree = require_once $file;
		}
		$menuModel = D('Menu');
		foreach ($tree as $value) {
			$add_pid = $menuModel->add(
				array(
					'title' => $value['title'],
					'url' => $value['url'],
					'pid' => $pid,
					'hide' => isset($value['hide']) ? (int) $value['hide'] : 0,
					'tip' => isset($value['tip']) ? $value['tip'] : '',
					'group' => $value['group'],
				)
			);
			if ($value['operator']) {
				$this->import($value['operator'], $add_pid);
			}
		}
	}

	/**
	 * @title 批量导入
	 * @author yangweijie <yangweijiester@gmail.com>
	 */
	public function import() {
		if ($this->request->isPost()) {
			$tree = $this->request->post('tree', '');
			$pid = $this->request->param('pid', 0);

			$lists = explode(PHP_EOL, $tree);

			if (empty($lists)) {
				return $this->error('请按格式填写批量导入的菜单，至少一个菜单');
			}

			$data = [];
			foreach ($lists as $value) {
				list($title, $url, $pid, $group) = explode('|', $value);
				if ($title != '' && $url != '' && $pid != '' && $group != '') {
					$data[] = ['title' => $title, 'url' => $url, 'pid' => $pid, 'sort' => 0, 'hide' => 0, 'tip' => '', 'is_dev' => 0, 'group' => $group];
				}
			}

			$result = (new MenuM())->saveAll($data);
			if (false !== $result) {
				Cache::pull('admin_menu_list');
				return $this->success('导入成功', url('/admin/menu/index'));
			}else{
				return $this->error('导入失败！');
			}
		} else {
			$pid = $this->request->param('pid', 0);
			$menu = MenuM::find($pid);

			$this->data = [
				'pid' => $pid,
				'menu' => $menu
			];
			return $this->fetch();
		}
	}
}