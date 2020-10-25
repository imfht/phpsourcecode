<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/6 22:07
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use think\Controller;
use app\admin\model\UserModel;
class Login extends Controller
{

    public function index(){
        $this->view->engine->layout(false);
        return $this->fetch('/login');
    }

    /**
     * 用户登录后台
     * $ajax提交的方式
     */
    public function doLogin(){
        $UserName = input('username');
        $PassWord = input('password');
        $Code = input('code');
        $result = $this->validate([
            'username'  => $UserName,
            'password' => $PassWord,
            'code'=>$Code
        ], 'LoginValidate');
        if(true !== $result){
            return json(easymsg(-1,'',$result));
        }
        if(!captcha_check($Code)){
            return json(easymsg(-2, '', '验证码错误'));
        };
        $userModel = new UserModel();
        $findUser = $userModel->checkUser($UserName);

        if(empty($findUser)){
            return json(easymsg(-3, '', '管理员不存在'));
        }
        if (md5($PassWord)!=$findUser['password']){
            return json(easymsg(-4, '', '密码错误'));
        }

        if(1 != $findUser['status']){
            return json(msg(-5, '', '该账号被禁用'));
        }

        session('uid',$findUser['uid']);
        session('user',$findUser);
     

        $param =  [
            'login_num' => $findUser['login_num'] + 1,
            'login_ip' => request()->ip(),
            'login_time' => time()
        ];

        $res = $userModel->updateStatus($param,$findUser['uid']);
        if(1 != $res['code']){
            return json(easymsg(-6, '', $res['msg']));
        }
        return json(easymsg(1, url('index/index'), '登录成功'));
    }


    public function out(){

        session('user', null);
        session('uid', null);
        session_destroy();
        $this->success('退出登录成功！', '@admin');
    }

}