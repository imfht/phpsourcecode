<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

use app\model\Member;
use app\model\MemberLog;
use app\model\Role;
use app\model\RoleAccess;
use xin\helper\Str;

/**
 * @title 用户管理
 */
class User extends Base {

	/**
	 * @title 用户列表
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function index(Member $user) {
		$list = $user->getUserList($this->request);
		$this->data['code'] = 1;
		$this->data['data'] = $list;
		return $this->data;
	}

	/**
	 * @title 用户详情
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function detail(Member $user) {
		$info = $user->getUserDetail($this->request);
		$this->data['code'] = 1;
		$this->data['data'] = $info;
		return $this->data;
	}

	/**
	 * @title 用户添加
	 * @method POST
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function add(Member $user) {
		$data = $this->request->post();
		$data['salt'] = Str::random(6);

		$result = $user->save($data);
		if (false !== $result) {
			$this->data['code'] = 1;
			$this->data['msg'] = '添加成功！';
		} else {
			$this->data['code'] = 0;
			$this->data['msg'] = '添加失败！';
		}
		return $this->data;
	}

	/**
	 * @title 用户编辑
	 * @method POST
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function edit(Member $user) {
		$data = $this->request->post();
		unset($data['password']);
		if (isset($data['repassword']) && $data['repassword'] != '') {
			$data['password'] = $data['repassword'];
			$data['salt'] = Str::random(6);
		}

		$result = $user->update($data, ['uid' => $data['uid']]);
		if (false !== $result) {
			$this->data['code'] = 1;
			$this->data['msg'] = '修改成功！';
		} else {
			$this->data['code'] = 0;
			$this->data['msg'] = '修改失败！';
		}
		return $this->data;
	}

	/**
	 * @title 用户删除
	 * @method GET
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function delete(Member $user) {
		$param = $this->request->param();

		if (isset($param['id']) && $param['id'] != '') {
			$result = $user->where('uid', $param['id'])->update(['status' => -1]);
			if (false !== $result) {
				$this->data['code'] = 1;
				$this->data['msg'] = '成功删除！';
			} else {
				$this->data['code'] = 0;
				$this->data['msg'] = '删除失败！';
			}
		} else {
			$this->data['code'] = 0;
			$this->data['msg'] = '非法操作！';
		}
		return $this->data;
	}

	/**
	 * @title 密码修改
	 * @method POST
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function editpasswd(Member $user) {
		$data = $this->request->post();

		$uid = (isset($data['uid']) && $data['uid']) ? $data['uid'] : $this->request->user['uid'];

		$userInfo = $user->where('uid', $uid)->find();

		if ($userInfo['password'] !== md5($data['oldpassword'] . $userInfo['salt'])) {
			$this->data['code'] = 0;
			$this->data['msg'] = "旧密码不正确！";
			return $this->data;
		}
		
		$save = [
			'salt' => Str::random(6),
			'password' => $data['password']
		];
		
		$result = $user->update($save, ['uid' => $uid]);

		if (false !== $result) {
			$this->data['code'] = 1;
			$this->data['msg'] = '修改成功！';
		} else {
			$this->data['code'] = 0;
			$this->data['msg'] = '修改失败！';
		}
		return $this->data;
	}

	/**
	 * @title 权限信息
	 * @method GET
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function authinfo(Member $user, Role $role) {
		$this->data['data'] = $role->getUserAuthInfo($this->request);
		$this->data['data']['userInfo'] = $user->getUserDetail($this->request);
		$this->data['data']['roles'] = $this->data['data']['module'];
		$this->data['data']['permission'] = [];
		
		$this->data['code'] = 1;
		return $this->data;
	}

	/**
	 * @title 更新权限
	 * @method POST
	 * @param  CustomerM $customer [description]
	 * @return [type]              [description]
	 */
	public function auth(Member $user, RoleAccess $role) {
		$data = $this->request->post();
		//更新部门信息
		$user->update(['department' => $data['department']], ['uid' => $data['uid']]);
		//更新角色信息
		$role->where('uid', $data['uid'])->delete();
		$role->save(['uid' => $data['uid'], 'group_id' => $data['role']]);

		$this->data['code'] = 1;
		$this->data['msg'] = "更新成功！";
		return $this->data;
	}

	/**
	 * 用户日志
	 * @param  MemberLog $log [description]
	 * @return [type]         [description]
	 */
	public function log(MemberLog $log) {
		$list = $log->getMemberLogList($this->request)->each(function ($item, $key) {
			$item['params'] = json_encode($item['param']);
			return $item;
		});

		$this->data['data'] = $list;
		return $this->data;
	}

	/**
	 * 用户日志
	 * @param  MemberLog $log [description]
	 * @return [type]         [description]
	 */
	public function clearlog(MemberLog $log) {
		$result = $log->where('create_time', '<', time())->delete();

		if (false !== $result) {
			$this->data['msg'] = '已清空！';
			$this->data['code'] = 1;
		} else {
			$this->data['msg'] = '未清空！';
			$this->data['code'] = 0;
		}
		return $this->data;
	}
	
	
	/**
	 * 左侧菜单
	 * @param  MemberLog $log [description]
	 * @return [type]         [description]
	 */
	public function getMenu(MemberLog $log) {
		$this->data['data'] = [
			[
				'label' => "客户管理",
				'path' => "/customer",
				'icon' => 'el-icon-document',
				'meta' => [
					'i18n' => 'customer',
				],
				'children' => [
					[
						'label' => "客户列表",
						'path' => "/index",
						'component' => 'views/customer/index',
						'icon' => 'el-icon-document',
						'meta' => [
							'i18n' => 'customer',
						],
					],
					[
						'label' => "厂商列表",
						'path' => "/firm",
						'component' => 'views/customer/index',
						'icon' => 'el-icon-document',
						'meta' => [
							'i18n' => 'customer',
						],
					],
					[
						'label' => "标注列表",
						'path' => "/named",
						'component' => 'views/customer/index',
						'icon' => 'el-icon-document',
						'meta' => [
							'i18n' => 'customer',
						],
					],
				]
			]
		];
		return $this->data;
	}
	
	/**
	 * 顶部菜单
	 * @param  MemberLog $log [description]
	 * @return [type]         [description]
	 */
	public function getTopMenu() {
		return $this->data;
	}
}