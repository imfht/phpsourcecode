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
use think\facade\Session;

/**
 * @title 用户中心
 */
class Index extends Base {

	/**
	 * @title 用户首页
	 * @return [type] [description]
	 */
	public function index() {
		return $this->fetch();
	}

	/**
	 * @title 用户登录
	 * @return [type] [description]
	 */
	public function login() {
		if ($this->request->isAjax()) {
			try {
				$userinfo = (new Member())->login($this->request);
				if ($userinfo) {
					Session::set('userInfo', $userinfo);
					return $this->success('登录成功！', url('/user/index/index'));
				}
			} catch (Exception $e) {
				return $this->error($e->getError(), '');
			}
		} else {
			return $this->fetch();
		}
	}

	/**
	 * @title 用户退出
	 * @return [type] [description]
	 */
	public function logout() {
		Session::delete('userInfo');
		$this->redirect('/user/index/login');
	}

	/**
	 * @title 用户注册
	 * @return [type] [description]
	 */
	public function register() {
		if ($this->request->isAjax()) {
			$result = (new Member())->register($this->request);
			if (false !== $result) {
				return $this->success("注册成功！", url('/user/index/login'));
			} else {
				return $this->error("注册失败！");
			}
		} else {
			return $this->fetch();
		}
	}

	/**
	 * @title 忘记密码
	 * @return [type] [description]
	 */
	public function forget() {
		if ($this->request->isAjax()) {
			$data = $this->request->post();
			$map = [];
			if (!$data['username'] || !$data['email']) {
				return $this->error("请完整填写信息！");
			}
			$map[] = ['username', '=', $data['username']];
			$map[] = ['email', '=', $data['email']];

			$user = Member::where($map)->findOrEmpty();
			if (!$user->isEmpty()) {
				//发生重置密码连接电子邮件
				$result = Member::sendFindPaswd($user);
				if (false !== $result) {
					return $this->success("已发送找回密码邮件！", url('/user/index/login'));
				} else {
					return $this->error("发送邮件失败！");
				}
			} else {
				return $this->error('无此用户！');
			}
		} else {
			return $this->fetch();
		}
	}

	/**
	 * @title 重置密码
	 * @return [type] [description]
	 */
	public function resetpasswd() {
		if ($this->request->isAjax()) {
			$token = $this->request->get('token');
			$data = $this->request->post();

			list($username, $email) = explode("|", \xin\helper\Secure::decrypt($token, \think\facade\Env::get('jwt.secret')));
			if (!$username || !$email) {
				return $this->error("找回密码地址错误或已过期！");
			}
			$map[] = ['username', '=', $username];
			$map[] = ['email', '=', $email];

			$user = Member::where($map)->findOrEmpty();

			if (!$user->isEmpty()) {
				$data['salt'] = \xin\helper\Str::random(6);
				$result = Member::update($data, ['uid' => $user['uid']]);
				if (false !== $result) {
					return $this->success("已重置！", url('/user/index/login'));
				} else {
					return $this->error("发送邮件失败！");
				}
			} else {
				return $this->error('无此用户！');
			}
		} else {
			$token = $this->request->param('token');
			return $this->fetch();
		}
	}
}