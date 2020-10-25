<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\user;

use app\model\Member;

/**
 * @title 用户管理
 */
class User extends Base {
	/**
	 * @title 个人资料
	 * @return [type] [description]
	 */
	public function profile() {
		if ($this->request->isPost()) {
			$result = (new Member())->editUser($this->request, session('userInfo.uid'));
			if (false !== $result) {
				return $this->success('修改成功！');
			} else {
				return $this->error('修改失败');
			}
		}else{
			$info = Member::find(session('userInfo.uid'));
			$this->data = [
				'info'  => $info,
				'keyList' => Member::$useredit
			];
			return $this->fetch('user@/edit');
		}
	}

	/**
	 * @title 重置密码
	 * @return [type] [description]
	 */
	public function repasswd() {
		if ($this->request->isAjax()) {
			$data = $this->request->post();

			$user = Member::where('uid', $data['uid'])->findOrEmpty();
			if (!$user->isEmpty()) {
				if (md5($data['oldpassword'] . $user['salt']) !== $user['password']) {
					return $this->error('旧密码不正确！');
				}

				$data['salt'] = \xin\helper\Str::random(6);
				$result = $user->save($data);

				if (false !== $result) {
					return $this->success('修改成功！');
				} else {
					return $this->error('修改失败');
				}
			}else{
				return $this->error('无此用户！');
			}
		}else{
			return $this->fetch();
		}
	}

	/**
	 * @title 上传头像
	 * @return [type] [description]
	 */
	public function avatar() {
		return $this->fetch();
	}
}