<?php
    /**
	 * @author swu_mao
	 * @time 2014.12.29
	 * 这个类是公告类，主要处理登录注册等功能
	*/
	class PublicAction extends Action{
		/**
		 * @param
		 * @return
		 * 显示登录页面
		*/
		public function login(){
			$this->display();
		}

		/**
		 * @param
		 * @return
		 * 登陆处理方法
		*/
		public function loginHandle(){
			if(session('code')!=md5($_POST['code'])){
				$this->error('验证码输入错误！');
			}
			$User = new UserModel();
			if($User->checkUser($this->_post('username'), $this->_post('password'))){
				session('username', $this->_post('username'));
				$this->success('登陆成功', U('Index/index'));
				return;
			}
			$this->error('用户名密码不匹配！');
		}

		/**
		 * @param
		 * @return
		 * 生成验证码方法
		*/
		public function verify(){
			import('ORG.Util.Image');
			Image::buildImageVerify(4, 1, 'png', 30, 22, 'code');
		}

		/**
		 * @param
		 * @return
		 * 注册方法
		*/
		public function register(){
			$this->display();
		}

		/**
		 * @param
		 * @return
		 * 注册处理方法
		*/
		public function registerHandle(){
			if($this->_post('password')!=$this->_post('check')){
				$this->error('两次输入的密码不一致！');
			}
			if($this->_post('username')=='' || $this->_post('password')==''){
				$this->error('用户名或密码不可以为空！');
			}
			$User = new UserModel();
			if($User->addUser($this->_post('username'), $this->_post('password'), $this->_post('name')==''?'用户':$this->_post('name'))){
				$this->success('注册成功！', U('Public/login'));
				return;
			}
			$this->error('注册失败！');
		}

		/**
		 * @param
		 * @return
		 * 登出方法
		*/
		public function logout(){
			session(null);
			$this->success('退出成功！', U('Index/index'));
		}
	}
?>