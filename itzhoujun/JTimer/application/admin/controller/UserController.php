<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/2
 * Time: 10:11
 */

namespace app\admin\controller;


use think\Db;
use think\Request;

class UserController extends AdminBaseController
{

    public function alterPwd(Request $request){

        if($request->isGet()) return $this->fetch();

        $result = $this->validate(
            $request->post(),
            [
                'password' => 'require',
                'new_password' => 'require',
                'confirm_password' => 'require|confirm:new_password',
            ],
            [
                'confirm_password.confirm' => '两次输入的新密码不同'
            ]
        );
        if(true !== $result){
            $this->error($result);
        }

        $user = $this->getCurrentUser();

        $old_password = encryPassword($request->post('password'),$user['salt']);

        if($old_password != $user['password']){
            $this->error('原密码不正确');
        }

        $new_password = encryPassword($request->post('new_password'),$user['salt']);

        if($new_password == $old_password){
            $this->error('新旧密码不能相同');
        }

        $result = Db::name('user')->update(['password'=>$new_password,'id' => $user['id']]);

        if($result){
            $user['password'] = $new_password;
            session('admin_user',$user);
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }

    }
}