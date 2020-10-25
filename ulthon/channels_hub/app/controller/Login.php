<?php

declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Admin;
use think\facade\Session;
use think\Request;

class Login extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        
        return view();
    }

    public function post(Request $request)
    {
        $post_data = $request->post();
        
        if(!captcha_check($post_data['captcha'])){
            // return $this->error('验证码错误');
        }
        $model_admin = Admin::where('account',$post_data['account'])->find();
        if(empty($model_admin)){
            return $this->error('用户不存在');
        }

        if(!$model_admin->verifyPassword($post_data['password'])){
            return $this->error('密码错误');
        }

        Session::set('admin_id',$model_admin->id);

        return $this->success('登陆成功','Index/index');
    }
}
