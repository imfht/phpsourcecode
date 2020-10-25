<?php
/**
 * @name LoginController
 * @desc 登录
 */
class LoginController extends AdminController {
    public function init(){
        parent::init();
        if($this->isLogin()) $this->redirect('/admin/index/index');
    }
    public function indexAction(){
        if($this->request->isPOST()){
            // 获取参数
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $isremember = $this->request->getPost('isremember', false);
            $user = SystemUser::find_by_username($username);
            if($user){
                // account info ok
                if($user->password == md5(md5($password) . $user->salt)){
                    // password is right
                    if($user->status == 1){
                        // 用户权限
                        $userLoginStatus = SystemUser::saveLoginStatus($user); 
                        var_dump($userLoginStatus);
                        if ($userLoginStatus['status'] == true){
                            return $this->redirect('/admin/index/index'); 
                        }else{
                            // 登录失败
                        }
                    }else{
                        $this->view->assign('errorMessage', '您的账户被锁定');
                    }
                }else{
                    // password fail
                    $this->view->assign('errorMessage', '您的账户或密码错误!');
                }
            }else{
                // not fond this account
                $this->view->assign('errorMessage', '您的账户或密码错误!');
            }
        }
    }
    /**
     * 退出
     */
    public function outAction(){
        $this->session->del('auth_manager_user');
        return $this->redirect('/admin/login/index');
    }
}