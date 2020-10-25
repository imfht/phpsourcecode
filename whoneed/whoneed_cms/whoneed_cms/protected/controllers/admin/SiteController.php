<?php
/**
 * 网站后台首页
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2012
 *
 */

	class SiteController extends MyAdminController{
		
		private $arrUAuth = array();

		// 初始化
		public function init()
        {
			parent::init();
		}

		// 后台首页
		public function actionIndex()
        {
			// 获取栏目列表
			$arrDataList = CF::funArrGetColumn();
//            print_r($arrDataList);
			
			// 获取当前用户所拥有的角色
			$arrAuth = array();
			$role_id = 0;
			$role_id = Yii::app()->user->getState('role_id');

			if(empty($role_id)){
				$intAid	  = Yii::app()->user->getState('admin_id');
				$objAUser = Whoneed_admin::model()->find("id = {$intAid}");
				if($objAUser){
					$role_id = $objAUser->role_id; 
				}			
			}
			
			// 获取相应的角色栏目
			if($role_id){
				$arrAuth = CF::getRoleColumn($role_id);
			}

			$data = array();
			$data['arrDataList'] = $arrDataList;
			$data['arrAuth']	 = $arrAuth;
			$this->render('/admin/site/index', $data);
		}

		// 后台登陆
		public function actionLogin()
		{
			if(Yii::app()->request->requestType == 'POST')
			{
				$username		= trim(Yii::app()->request->getParam('User'));
				$password		= Yii::app()->request->getParam('Pass');
				$verifyCode		= Yii::app()->request->getParam('authCode');
				$strAuthCode	= $_SESSION['authCode'];

				// 验证码
				if($verifyCode == $strAuthCode){
					// 用户验证类
					$identity = new MyUserIdentity($username, $password);
					if($identity->admin_authenticate()){
						//登陆成功
						Yii::app()->user->login($identity, 3600*24*7);
						Yii::app()->request->redirect('/admin/site/index');
					}else{
						// 登陆失败
						MyFunction::funAlert('用户名或密码错误!', '/admin/site/index');
					}
				}else{
					// 验证码错误
					MyFunction::funAlert('验证码输入错误!', '/admin/site/index');					
				}
			}
			else
			{
				//已经登陆跳转到首页
				if(!Yii::app()->user->isGuest && Yii::app()->user->getState('admin_is_login')){
					Yii::app()->request->redirect('/admin/site/index');
				}
			}

			//显示登陆界面
			$this->render('/admin/site/index');
		}

		// 后台注销
		public function actionLogout(){
			Yii::app()->user->logout();
			echo "<script>top.location='/admin/site'</script>";		
		}

        //修改密码
        public function actionChangePass(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                //验证两次密码是否一致
                $oldPass = trim(Yii::app()->request->getParam('old_pass'));
                $newPass = trim(Yii::app()->request->getParam('new_pass'));
                $newPassAgin = trim(Yii::app()->request->getParam('new_pass_agin'));
                if($newPass !== $newPassAgin){
                    $this->alert_error('两次密码不一致，请重新输入');
                    exit;
                }

                if( preg_match('/^[\d]{0,6}$/',$newPass) ){
                    $this->alert_error('新密码设置过于简单,请重新设置!');
                    exit;
                }

                //验证原始密码是否正确
                $adminName = Yii::app()->user->getName();

                $whoneed_model = Whoneed_admin::model();
                $user = $whoneed_model->find("user_name='{$adminName}'");
                if(MyFunction::funHashPassword($oldPass,TRUE) !== $user->user_pass){
                    $this->alert_error('原始密码不正确');
                    exit;
                }

                //修改密码
                $user->user_pass = MyFunction::funHashPassword($newPassAgin,TRUE);
                if($user->save()){
                    Yii::app()->user->logout();
                    $this->alert_ok('修改成功，请退出重新登入');
                }else{
                    $this->alert_error();
                }
            }
            $this->display('change_pass');
        }

        public function actionWeakPassChange(){
            if(Yii::app()->request->isPostRequest){
                //验证两次密码是否一致
                $oldPass = trim(Yii::app()->request->getParam('old_pass'));
                $newPass = trim(Yii::app()->request->getParam('new_pass'));
                $newPassAgin = trim(Yii::app()->request->getParam('new_pass_agin'));
                if($newPass !== $newPassAgin){
                    $this->alert_error('两次密码不一致，请重新输入');
                    exit;
                }

                if( preg_match('/^[\d]{0,6}$/',$newPass) ){
                    $this->alert_error('新密码设置过于简单,请重新设置!');
                    exit;
                }

                //验证原始密码是否正确
                $adminName = Yii::app()->user->getName();

                $whoneed_model = Whoneed_admin::model();
                $user = $whoneed_model->find("user_name='{$adminName}'");
                if(MyFunction::funHashPassword($oldPass,TRUE) !== $user->user_pass){
                    $this->alert_error('原始密码不正确');
                    exit;
                }

                //修改密码
                $user->user_pass = MyFunction::funHashPassword($newPassAgin,TRUE);
                if($user->save()){
                    Yii::app()->user->logout();
                    $this->alert_ok('修改成功，请退出重新登入');
                }else{
                    $this->alert_error();
                }
            }
            $this->display('weakPassChange');
        }

        public function actionFlush()
        {
            Yii::app()->cache->flush();
            echo 'flush success!';
        }
	}
?>
