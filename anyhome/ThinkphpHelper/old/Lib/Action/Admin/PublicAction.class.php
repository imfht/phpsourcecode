<?php
class PublicAction extends Action {
    public function ckLogin()
    {
        $account = I('account');
        $pw = I('password');
        if ($account =='' || $pw == '') {
            $this->error('请输入用户名或密码');
            return;
        }
        $Member = M('Member');
        $map['account|email'] = $account;
        $map['password'] = md5($password);
        $minfo = $Member->where($map)->find();
        if ($minfo) {
            session('mid',$minfo['id']);
            $this->success('登录成功',U('Admin/Index/index'));
        }else{
            $this->error('用户名或密码错误');
        }
    }
    


    public function logout()
    {
        session('mid',null);
        $this->success('登出成功',U('Admin/Public/login'));
    }

}