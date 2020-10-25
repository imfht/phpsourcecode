<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\User as Users;
use app\common\model\TokenUser;

class Login extends Controller {
    private $cModel;   //当前控制器关联模型

    public function initialize() {
//      parent::initialize();
//		define('H_NAME', request()->domain());	//获取当前域名,包含"http://"
//		define('M_NAME', request()->module());	//当前模块名称
//      define('C_NAME', request()->controller());	//当前控制器名称
//      define('A_NAME', request()->action());	//当前操作名称
        $this->cModel = new Users;   //别名：避免与控制名冲突

    }

    public function index() {
        $userId = session('userId');
        if (!empty($userId)){
            $this->redirect('index/index');
        }else{
            return $this->fetch();
        }
    }

    public function checkLogin() {	//登录
        if(request()->isPost()){
            $data = input('post.');
            if(!captcha_check($data['code'])){
                return ajaxReturn('验证码错误');
            };
			$login = $this->cModel->login( $data['username'],md5($data['password']) );
			if( $login ){
				return ajaxReturn('登录成功',url('Index/index') );
			}else{
				return ajaxReturn($this->cModel->error);
			}
        }
    }

    public function loginOut($params='') {
        session('userId', null);
        session('user_token', null);
        $this->redirect('Login/index', $params);
    }

    public function restLogin(){
        return $this->fetch();
    }

}
