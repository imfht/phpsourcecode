<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;

class Profile extends Common
{
    public function index($act=null)
    {
        if ($act=='update') {
            $data['avatar'] = input('post.avatar');
            $data['sex'] = input('post.sex/d');
            $data['password'] = input('post.password');
            $data['birthday'] = strtotime(input('post.birthday'));
            $data['tel'] = input('post.tel');
            $data['qq'] = input('post.qq');
            $data['email'] = input('post.email');
            if ($data['password']=='') {
                unset($data['password']);
            } else {
                $data['password'] = password($data['password']);
            }
            $r = Db::name('user')->where(['uid'=>$this->user['uid']])->update($data);
            if ($r) {
                addlog('修改个人资料，ID：'.$this->user['uid'], $this->user['username']);
                return $this->success('恭喜，个人资料修改成功！', url('admin/profile/index'));
            } else {
                return $this->error('参数错误，请重试！');
            }
        }
        return View::fetch();
    }
}
