<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

// 后台用户模块
class UserAction extends CommonAction {
	
		public function _before_insert(){
				$this->checkAdmin();
		}
		
		public function _before_update(){
				$this->checkAdmin();
		}
		
		public function _before_delete(){
				$this->checkAdmin();
		}		
		
		public function _before_edit(){
				$this->checkAdmin();
		}		
			
		// 检查帐号
		public function checkAccount() {
				$User = D("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $User->getByAccount($name);
        if($result) {
        	$this->error('该用户名已经存在！');
        }
    }

		// 插入数据
		public function insert() {
			$this->checkAdmin();
			$this->checkAccount();
			// 创建数据对象
			$User	 =	 D("User");
			if(!$User->create()) {
				$this->error($User->getError());
			}else{
				// 写入帐号数据
				if($result	 =	 $User->add()) {
					$this->success('用户添加成功！');
				}else{
					$this->error('用户添加失败！');
				}
			}
		}
	
		public function profile() {
			$User	 =	 M("User");
			$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
			$this->assign('vo',$vo);
			$this->display();
		}
	
		// 修改资料
		public function change() {
			$User	 =	 D("User");
			if(!$User->create()) {
				$this->error($User->getError());
			}
			if($_POST['nickname'])cookie('admin_nickname', $_POST['nickname'], 8640000);
			$id  =  $_POST['id'];
			$User->id			=	$id;
			$result	=	$User->save();
			if(false !== $result) {
				$this->success('资料修改成功！');
			}else{
				$this->error('资料修改失败!');
			}
		}

    // 更换密码
    public function changePwd()
    {
		    //对表单提交处理进行处理或者增加非表单数据
				if(md5($_POST['verify'])	!= $_SESSION['verify']) {
					$this->error('验证码错误！');
				}
				$map	=	array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(session(C('USER_AUTH_KEY'))) {
            $map['id']		=	session(C('USER_AUTH_KEY'));
        }
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
        		$User->id	=	session(C('USER_AUTH_KEY'));
						$User->password	=	pwdHash($_POST['password']);
						$User->save();
						$this->assign('jumpUrl',__APP__.'/Admin/Index');
						$this->success('密码修改成功！');
        }
    }

    //编辑用户
    public function update()
    {
    		$this->checkAdmin();
    		$id  =  $_POST['id'];
        $password = $_POST['resetpwd'];
        unset($_POST['resetpwd']);
        if($password) {
        	$_POST['password']=pwdHash($password);
        }
        $User = D('User');
				if(!$User->create()) {
					$this->error($User->getError());
				}
				$User->id	=	$id;
				$result	=	$User->save();
        if(false !== $result) {
            $this->success("用户资料修改成功！");
        }else {
        	$this->error('用户资料修改失败！');
        }
    }
	    
}
?>