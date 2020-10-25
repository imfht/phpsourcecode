<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use think\facade\Db;
use app\model\AuthGroup;
use app\model\AuthGroupAccess;
use app\model\AuthRule;
use app\model\Model;

/**
 * @title 用户组管理
 * @description 用户组管理
 */
class Group extends Base {

	/**
	 * @title 用户组列表
	 */
	public function index($type = 'admin') {
		$map = [];

		$map[] = ['module', '=', $type];

		$list = AuthGroup::where($map)->order('id desc')->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'page' => $list->render(),
			'type' => $type,
		);
		return $this->fetch();
	}

	/**
	 * @title 添加用户组
	 */
	public function add($type = 'admin') {
		if ($this->request->isPost()) {
			$data = $this->request->param();

			$result = AuthGroup::create($data);
			if (false !== $result) {
				return $this->success("添加成功！", url('/admin/group/index'));
			} else {
				return $this->error("添加失败！");
			}
		} else {
			$this->data = array(
				'info' => ['module' => $type, 'status' => 1],
				'keyList' => (new AuthGroup())->keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑用户组
	 */
	public function edit($id) {
		if ($this->request->isPost()) {
			$data = $this->request->param();
			$result = AuthGroup::update($data);
			if (false !== $result) {
				return $this->success("编辑成功！", url('/admin/group/index'));
			} else {
				return $this->error("编辑失败！");
			}
		} else {
			if (!$id) {
				return $this->error("非法操作！");
			}
			$info = AuthGroup::find($id);
			$this->data = array(
				'info' => $info,
				'keyList' => (new AuthGroup())->keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑用户组单字段
	 */
	public function editable() {
		$name = $this->request->param('name', '');
		$value = $this->request->param('value', '');
		$pk = $this->request->param('pk', '');

		if ($name && $value && $pk) {
			$save[$name] = $value;
			AuthGroup::update($save, ['id' => $pk]);
		}
	}

	/**
	 * @title 删除用户组
	 */
	public function del() {
		$id = $this->request->param('id', 0);
		$map = [];

		if (!$id) {
			return $this->error("非法操作！");
		}
		$map[] = is_array($id) ? ['id', 'IN', $id] : ['id', '=', $id];

		$result = AuthGroup::where($map)->delete();
		if (false !== $result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}

	/**
	 * @title 权限节点
	 */
	public function access($type = 'admin') {
		$map = [];

		$map[] = ['module', '=', $type];

		$list = AuthRule::where($map)->order('id desc')->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'page' => $list->render(),
			'type' => $type,
		);
		return $this->fetch();
	}

	/**
	 * @title 更新权限
	 */
	public function upnode($type) {
		$result = AuthRule::uprule($type);
		if (false !== $result) {
			return $this->success("更新成功！");
		}else{
			return $this->error("更新失败！");
		}
	}

	/**
	 * @title 用户组授权
	 */
	public function auth($id) {
		if ($this->request->isPost()) {
			$rule = $this->request->post('rule', []);
			$extend_rule = $this->request->post('extend_rule', []);
			$extend_result = $rule_result = false;
			//扩展权限
			$extend_data = [];
			foreach ($extend_rule as $key => $value) {
				foreach ($value as $item) {
					$extend_data[] = array('group_id' => $id, 'extend_id' => $item, 'type' => $key);
				}
			}
			if (!empty($extend_data)) {
				Db::name('AuthExtend')->where(array('group_id' => $id))->delete();
				$extend_result = Db::name('AuthExtend')->insertAll($extend_data);
			}
			if ($rule) {
				$rules = implode(',', $rule);
				$rule_result = AuthGroup::update(['rules'=> $rules], ['id' => $id]);
			}

			if ($rule_result !== false || $extend_result !== false) {
				return $this->success("授权成功！", url('/admin/group/index'));
			} else {
				return $this->error("授权失败！");
			}
		} else {
			if (!$id) {
				return $this->error("非法操作！");
			}
			$group = AuthGroup::find($id);

			$map[] = ['module', '=', $group['module']];
			$row = AuthRule::where($map)->order('id desc')->select();

			$list = array();
			foreach ($row as $value) {
				$list[$value['group']][] = $value;
			}

			//模块
			$model = Model::where('status', '>', 0)->field('id,title,name')->select();
			//扩展权限
			$extend_auth = Db::name('AuthExtend')->where(array('group_id' => $id, 'type' => 2))->column('extend_id');
			$this->data = array(
				'list' => $list,
				'model' => $model,
				'extend_auth' => $extend_auth,
				'auth_list' => $group['rules'],
				'id' => $id,
			);
			return $this->fetch();
		}
	}

	/**
	 * @title 添加节点
	 */
	public function addnode($type = 'admin') {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = AuthRule::create($data);
			if ($result) {
				return $this->success("创建成功！", url('/admin/group/access'));
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$this->data = [
				'info' => ['module' => $type, 'status' => 1],
				'keyList' => (new AuthRule())->keyList,
			];
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑节点
	 */
	public function editnode($id) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = AuthRule::update($data);
			if (false !== $result) {
				return $this->success("更新成功！", url('/admin/group/access'));
			} else {
				return $this->error("更新失败！");
			}
		} else {
			if (!$id) {
				return $this->error("非法操作！");
			}
			$info = AuthRule::find($id);
			$this->data = [
				'info' => $info,
				'keyList' => (new AuthRule())->keyList,
			];
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除节点
	 */
	public function delnode($id) {
		if (!$id) {
			return $this->error("非法操作！");
		}
		$result = AuthRule::find($id)->delete();
		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}
}