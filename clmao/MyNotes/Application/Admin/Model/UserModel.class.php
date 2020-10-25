<?php

namespace Admin\Model;

use Think\Model;

class UserModel extends Model {

    protected $_validate = array(
        array('user', '1,16', '用户名长度为1-16个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('user', '', '用户名被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('pwd1', 'checkPwd', '旧密码不正确', 0, 'callback', self::MODEL_UPDATE),
        array('pwd2', 'pwd3', '确认密码不正确', 0, 'confirm', self::MODEL_UPDATE),
    );

    //查看密码是否正常
    public function checkPwd($pwd1) {
        $user = session('admin');
        $pwd = clmao_md5_half($pwd1);
        $id = M('user')->where(array('user' => $user, 'pwd' => $pwd))->getField('id');
        if (empty($id)) {
            return false;
        } else {
            return true;
        }
    }

    //用户安全退出
    public function sale_exit() {
        $user = session('admin');
        session('admin',null);
        cookie('identifier', null);
        cookie('token', null);
        M('user')->where(array('user'=>$user))->save(array('token'=>  uniqid()));
        redirect(U('Home/Index/login'), 0);
    }

}
