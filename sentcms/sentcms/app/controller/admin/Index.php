<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\Member;
use think\facade\Session;

/**
 * @title 后端公共模块
 */
class Index extends Base {

	/**
	 * @title 后台首页
	 * @return html [description]
	 */
	public function index() {
		//判断安装文件是否删除
		$this->data['install_file'] = false;
		$file = $this->app->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'install.php';
		if (is_file($file)) {
			$this->data['install_file'] = true;
		}
		return $this->fetch();
	}

	/**
	 * @title 系统更新
	 */
	public function update(){
		$version = new \com\Version();
		if($this->request->isPost()){
			switch ($type) {
				case 'down':
					//下载升级包
					$this->data['data'] = $version->downloadZip();
					break;
				case 'unzip':
					//解压升级包
					$this->data['data'] = $version->unzipFile();
				case 'move':
					//覆盖文件
				case 'sql':
					//升级数据库
				default:
					$this->data['data'] = "无操作";
					break;
			}
			$this->data['code'] = 1;
			return $this->data;
		}else{
			$info = $version->check();
			
			$this->data['info'] = $info;
			return $this->fetch();
		}
	}

	/**
	 * @title 用户登录
	 * @return html
	 */
	public function login(Member $user, $username = '', $password = '', $verify = '') {
		if ($this->request->isPost()) {
			if (!$username || !$password) {
				return $this->error('用户名或者密码不能为空！', '');
			}

			//验证码验证
			if (!captcha_check($verify)) {
				return $this->error('验证码错误！', '');
			}

			try {
				$userinfo = $user->login($this->request);
				if ($userinfo) {
					Session::set('adminInfo', $userinfo);
					return $this->success('登录成功！', url('/admin/index/index'));
				}
			} catch (Exception $e) {
				return $this->error($e->getError(), '');
			}
		} else {
			return $this->fetch();
		}
	}

	/**
	 * @title 后台退出
	 * @return html
	 */
	public function logout(Member $user) {
		Session::delete('adminInfo');
		$this->redirect('/admin/login');
	}

	/**
	 * @title 清除缓存
	 * @return html
	 */
	public function clear() {
		if ($this->request->isPost()) {
			$clear = input('post.clear/a', array());
			foreach ($clear as $key => $value) {
				if ($value == 'cache') {
					\think\facade\Cache::clear(); // 清空缓存数据
				} elseif ($value == 'log') {
					\think\facade\Log::clear();
				}
			}
			return $this->success("更新成功！", url('/admin/index/clear'));
		} else {
			$keylist = array(
				array('name' => 'clear', 'title' => '更新缓存', 'type' => 'checkbox', 'help' => '', 'option' => array(
					'cache' => '缓存数据',
					'log' => '日志数据',
				),
				),
			);
			$this->data = array(
				'keyList' => $keylist,
			);
			return $this->fetch('admin/public/edit');
		}
	}
}