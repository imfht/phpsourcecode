<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

use app\model\Department as DepartmentM;
use app\model\Role;
use sent\tree\Tree;

/**
 * @title 部门管理
 */
class Department extends Base {

	/**
	 * @title 部门列表
	 * @param  DepartmentM $depart [description]
	 * @return [type]              [description]
	 */
	public function index(DepartmentM $depart) {
		$param = $this->request->param();
		$tree = isset($param['tree']) ? $param['tree'] : 0;
		$map = [];

		if (isset($param['name']) && $param['name'] != '') {
			$map[] = ['name', 'LIKE', '%' . $param['name'] . '%'];
		}
		if (isset($param['status']) && $param['status'] != '') {
			$map[] = ['status', '=', $param['status']];
		}

		$list = $depart->where($map)->select()->toArray();
		
		if($tree == 1){
			$tree = (new Tree())->listToTree($list, 'id', 'pid', 'children');
		}else{
			$tree = (new Tree())->toFormatTree($list);
		}

		$this->data['data'] = $tree;
		return $this->data;
	}

	/**
	 * @title 部门添加
	 * @method POST
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function add(DepartmentM $depart) {
		$data = $this->request->post();

		$result = $depart->save($data);
		if (false !== $result) {
			$this->data['code'] = 1;
			$this->data['msg'] = '添加成功！';
		}else{
			$this->data['code'] = 0;
			$this->data['msg'] = '添加失败！';
		}
		return $this->data;
	}

	/**
	 * @title 部门编辑
	 * @method POST
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function edit(DepartmentM $depart) {
		$data = $this->request->post();

		$result = $depart->update($data, ['id'=>$data['id']]);
		if (false !== $result) {
			$this->data['code'] = 1;
			$this->data['msg'] = '修改成功！';
		}else{
			$this->data['code'] = 0;
			$this->data['msg'] = '修改失败！';
		}
		return $this->data;
	}

	/**
	 * @title 角色列表
	 * @param  Role   $role [description]
	 * @return [type]       [description]
	 */
	public function role(Role $role) {
		$list = $role->getDataList($this->request)
			->append(['status_text'])
			->toArray();

		$this->data['data'] = $list;
		return $this->data;
	}

	/**
	 * @title 角色添加
	 * @method POST
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function addrole(Role $role) {
		$data = $this->request->post();

		$result = $role->save($data);
		if (false !== $result) {
			$this->data['code'] = 1;
			$this->data['msg'] = '添加成功！';
		}else{
			$this->data['code'] = 0;
			$this->data['msg'] = '添加失败！';
		}
		return $this->data;
	}

	/**
	 * @title 角色编辑
	 * @method POST
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function editrole(Role $role) {
		$data = $this->request->post();

		$result = $role->update($data, ['id'=>$data['id']]);
		if (false !== $result) {
			$this->data['code'] = 1;
			$this->data['msg'] = '修改成功！';
		}else{
			$this->data['code'] = 0;
			$this->data['msg'] = '修改失败！';
		}
		return $this->data;
	}

	/**
	 * @title 删除角色
	 * @method GET
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function delrole(Role $role) {
		$param = $this->request->param();
		if (!isset($param['id']) || !$param['id']) {
			$this->data['code'] = 0;
			$this->data['msg'] = '非法操作！';
		}

		$result = $role->where('id', $param['id'])->delete();
		if (false !== $result) {
			$this->data['code'] = 1;
			$this->data['msg'] = '删除成功！';
		}else{
			$this->data['code'] = 0;
			$this->data['msg'] = '删除失败！';
		}
		return $this->data;
	}
}