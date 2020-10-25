<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\controller\admin;

use think\facade\Cache;
use app\model\Member;
use app\model\AuthGroup;
use app\model\AuthGroupAccess;

/**
 * @title 用户管理
 */
class User extends Base {

	/**
	 * @title 用户列表
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index(Member $member) {
		$list = $member->getUserList($this->request);

		$this->data['list'] = $list;
		$this->data['page'] = $list->render();

		return $this->fetch();
	}

	/**
	 * @title 添加用户
	 * @author colin <molong@tensent.cn>
	 */
	public function add(Member $member) {
		if ($this->request->isPost()) {
			//创建注册用户
			$result = $member->register($this->request);
			if ($result) {
				return $this->success('用户添加成功！', url('/admin/user/index'));
			} else {
				return $this->error($model->getError());
			}
		} else {
			$this->data = array(
				'info' => [],
				'keyList' => $member->addfield,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 修改用户
	 * @author huajie <banhuajie@163.com>
	 */
	public function edit(Member $member) {
		if ($this->request->isPost()) {
			$reuslt = $member->editUser($this->request);
			if (false !== $reuslt) {
				return $this->success('修改成功！', url('/admin/user/index'));
			} else {
				return $this->error('修改失败');
			}
		} else {
			$info = $this->getUserinfo();

			$this->data = array(
				'info' => $info,
				'keyList' => $member->editfield,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除用户
	 * @author colin <colin@tensent.cn>
	 */
	public function del() {
		$uid = $this->request->param('id');

		if ($this->request->rootUid == $uid) {
			return $this->error('超级用户无法删除！');
		}

		//获取用户信息
		$result = Member::where('uid', $uid)->delete();
		if (false !== $result) {
			return $this->success('删除用户成功！');
		}else{
			return $this->error('删除失败！');
		}
	}

	/**
	 * @title 用户授权
	 * @author colin <colin@tensent.cn>
	 */
	public function auth() {
		$uid = $this->request->param('id', 0, 'trim,intval');
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$config = Cache::get('system_config_data');
			$group_type = isset($config['user_group_type']) ? $config['user_group_type'] : [];
			$add = [];
			foreach ($group_type as $value) {
				if (isset($data[$value['key']]) && $data[$value['key']]) {
					$add[] = ['uid' => $uid, 'group_id' => $data[$value['key']]];
				}
			}
			AuthGroupAccess::where('uid', $uid)->delete();
			$result = (new AuthGroupAccess())->saveAll($add);
			if (false !== $result) {
				return $this->success("设置成功！");
			}else{
				return $this->error('设置失败！');
			}
			
		} else {
			$row = AuthGroup::select();
			$auth = AuthGroupAccess::where(array('uid' => $uid))->select();

			$auth_list = array();
			foreach ($auth as $key => $value) {
				$auth_list[] = $value['group_id'];
			}
			foreach ($row as $key => $value) {
				$list[$value['module']][] = $value;
			}
			$this->data = array(
				'uid' => $uid,
				'auth_list' => $auth_list,
				'list' => $list,
			);
			return $this->fetch();
		}
	}

	/**
	 * @title 获取某个用户的信息
	 * @var uid 针对状态和删除启用
	 * @var pass 是查询password
	 * @var errormasg 错误提示
	 * @author colin <colin@tensent.cn>
	 */
	private function getUserinfo($uid = null, $pass = null, $errormsg = null) {
		//如果无UID则修改当前用户
		$uid = $uid ? $uid : $this->request->param('uid', session('userInfo.uid'));
		$map['uid'] = $uid;
		if ($pass != null) {
			unset($map);
			$map['password'] = $pass;
		}
		$list = Member::where($map)->field('uid,username,nickname,sex,email,qq,score,signature,status,salt')->find();
		if (!$list) {
			return $this->error($errormsg ? $errormsg : '不存在此用户！');
		}
		return $list;
	}

	/**
	 * @title 修改密码
	 * @author huajie <banhuajie@163.com>
	 */
	public function editpwd() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$data['salt'] = \xin\helper\Str::random(6);

			$reuslt = Member::update($data, ['uid' => $data['uid']]);

			if (false !== $reuslt) {
				return $this->success('修改成功！', url('/admin/user/index'));
			} else {
				return $this->error('修改失败');
			}
		} else {
			return $this->fetch();
		}
	}

	/**
	 * @title 会员状态修改
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function changeStatus($method = null) {
		$id = array_unique((array) input('id', 0));
		if (in_array(config('user_administrator'), $id)) {
			return $this->error("不允许对超级管理员执行该操作!");
		}
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			return $this->error('请选择要操作的数据!');
		}
		$map['uid'] = array('in', $id);
		switch (strtolower($method)) {
		case 'forbiduser':
			$this->forbid('Member', $map);
			break;

		case 'resumeuser':
			$this->resume('Member', $map);
			break;

		case 'deleteuser':
			$this->delete('Member', $map);
			break;

		default:
			return $this->error('参数非法');
		}
	}
}