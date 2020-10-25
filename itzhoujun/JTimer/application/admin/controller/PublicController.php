<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/27
 * Time: 18:11
 */

namespace app\admin\controller;


use think\Controller;
use think\Db;
use think\Request;

class PublicController extends Controller
{

    public function login(){

        $request = Request::instance();
        $is_validate = getSetting('show_validate_code');
        if($request->isGet()){
            $this->assign('validate',$is_validate);
            return $this->fetch();
        }

        $result = $this->validate(
            $request->post(),
            ['username' => 'require',      'password' => 'require'],
            ['username' => '用户名不能为空', 'password' => '密码不能为空',]
        );
        if($result !== true){
            $this->error($result);
        }

        if($is_validate && !captcha_check($request->post('validate_code'))){
            $this->error('验证码有误');
        }

        $user = Db::name('user')
            ->where('username',$request->post('username'))
            ->find();


        $password = encryPassword($request->post('password'),$user['salt']);
        if(empty($user) || $password != $user['password']){
            $this->error('用户名或密码不正确');
        }
        session('admin_user',$user);
        $this->success('登录成功',url('index/index'));

    }

    public function logout(){
        session('admin_user',null);
        $this->redirect(url('login'));
    }
}