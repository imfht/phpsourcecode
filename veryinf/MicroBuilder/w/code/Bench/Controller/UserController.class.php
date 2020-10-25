<?php
/**
 * 工作台欢迎页
 */
namespace Bench\Controller;
use Core\Model\Acl;
use Core\Model\Utility;
use Think\Controller;
class UserController extends Controller {

    public function profileAction() {
        $user = session('user');
        $u = new Acl();
        $user = $u->getUser($user['username']);
        if(IS_POST) {
            $user['password'] = I('post.password');
            $ret = $u->modifyUser($user['uid'], $user);
            if($ret === false) {
                $this->error('保存用户信息失败, 请稍后重试');
            } else {
                $this->success('保存成功');
                exit;
            }
        }
        $this->assign('user', $user);
        $this->display('profile');
    }
}