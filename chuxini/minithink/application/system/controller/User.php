<?php
namespace app\system\controller;
/*
*
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/2
*/
use app\system\model\Auth;
use think\Loader;
use app\base\controller\System;
use app\system\model\User as UserModel;
use think\Session;

class User extends System
{

    public function index()
    {

        $auth = Auth::all();

        $this->view->assign('role_list',$auth);
        return $this->view->fetch();
    }

    public function auth()
    {
        $menu = $this->getMenu();
        $this->view->assign('menu',$menu);
        return $this->view->fetch();
    }

    public function getusers($p = 1, $keyword = '') {
        $user = new UserModel();
        $p = ($p*10) - 10;
        $list = $user->getUser($keyword, $p);
        $msg['status'] =200;
        $msg['data']['list'] = $list;
        $msg['pages'] = $user->getPage();
        return $msg;
    }

    public function getauths($p = 1, $keyword = '') {
        $auth = new Auth();
        $p = ($p*10) - 10;
        $list = $auth->getAuth($keyword, $p);
        $msg['status'] =200;
        $msg['data']['list'] = $list;
        $msg['pages'] = $auth->getPage();
        return $msg;
    }

    /**
     * 新增，修改 用户
     * @return array|string
     */
    public function save() {
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if(empty($post_data)){return getMsg("数据不能为空");}
            $validate = Loader::validate('User');//验证器
            if(!$validate->check($post_data)){
                return getMsg($validate->getError());
            }

            $user = new UserModel();
            $state = $user->save_user($post_data);
            if(false == $state){
                return getMsg("操作失败");
            }
            return getMsg("操作成功","reload");
        }
    }
    /**
     * 新增，修改 角色权限
     * @return array|string
     */
    public function save_auth() {
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if(empty($post_data)){return getMsg("数据不能为空");}
            $node = '';//拼接节点ID
            while (list($key, $val) = each($post_data)){
                if(is_numeric($key)){
                    $node .= $val.',';
                    unset($post_data[$key]);
                }
            }
            $post_data['node'] = rtrim($node,',');

            $auth = new Auth();
            $state = $auth->allowField(true)->save($post_data, $post_data['id']);
            if(false == $state){
                return getMsg("操作失败");
            }
            return getMsg("操作成功","reload");
        }
    }

    /**
     * 管理员单独修改自己的密码
     */
    public function update_password() {
        $user = UserModel::get(Session::get('system_user')->id);
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if($post_data['new_pwd'] != $post_data['new_pwd2']){
                return getMsg("两次新密码不一致");
            }
            if($user->password != auth_password($post_data['pwd'])){
                return getMsg("原密码错误");
            }else{
                $user->password = auth_password($post_data['new_pwd']);
                $user->save();
                return getMsg("修改成功");
            }
        }else{
            $this->view->assign('info',$user);
            return $this->view->fetch();
        }
    }

}
