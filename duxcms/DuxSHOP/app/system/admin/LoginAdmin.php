<?php
/**
 * 登录控制器
 */
namespace app\system\admin;

class LoginAdmin extends \app\base\controller\BaseController {

    /**
     * 登录页
     */
    public function index(){
        if(!isPost()){
            $this->assign('sysInfo', $this->sysInfo);
            $this->display();
        }else{
            $userName = request('post', 'username');
            $passWord = request('post', 'password');
            if (empty($userName) || empty($passWord)) {
                $this->error('用户名或密码未填写！');
            }
            if (target('system/SystemUser')->setLogin($userName, $passWord)) {
                $this->success('登录系统成功！', url('system/Index/index'));
            } else {
                $this->error(target('system/SystemUser')->getError());
            }
        }
    }

    /**
     * 退出登录
     */
    public function logout() {
        target('system/SystemUser')->logout();
        $this->redirect(url('index'));
    }
}