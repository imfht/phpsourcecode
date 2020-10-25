<?php
namespace app\admin\controller;

use app\admin\model\Administrator as AdminModel;
use think\Validate;

class Administrator extends Base
{
    private $_rule = [
        'username' => 'require|length:4,25',
        'password' => 'length:4,32',
        'mobile' => 'require|length:11',
    ];

    private $defaultSide = 17;

    public function index()
    {
        $sideId = $this->getSideId();
        $showId = $sideId ? $sideId: $this->defaultSide;
        trace('showId:'.$showId);
        $this->assign('defaultSide', $showId);
        return $this->fetch('base/index');
    }

    public function setSideId()
    {
        # code...
    }

    public function getSideId()
    {
        # code...
    }

    public function userlist()
    {
        // setcookie('xxxx', 'xxxxxxxxxxxx', time()+86400);
        $this->lists('administrator', ['status' => array('egt', 0)], 'id desc');
        // $list = db('administrator')->where(['status'=>array('egt', 0)])->paginate(10);
        // \app\lib\intParse::int_to_string($list);
        // $this->assign('_list', $list);
        return $this->fetch();
    }

    /**
     * 用户编辑
     * @author EchoEasy
     */
    public function user_edit()
    {
        $id = $this->inputOrError('id', '用户ID必须');
        $info = AdminModel::get($id)->toArray();
        if ($this->request->isPost()) {

        }

        $map = ['module' => 'admin', 'status' => ['EGT', 0]];
        $groupList = db('AuthGroup')->where($map)->order('id asc')->paginate(10);
        $this->assign('_groupList', $groupList);
        $this->assign('_info', $info);
        $this->assign('_groupList', $groupList);
        return $this->fetch();
    }

    public function user_add()
    {
        return $this->fetch('user_edit');
    }

    /**
     * 用户保存
     * @author baiyouwen
     */
    public function save()
    {
        $id = $this->request->param('id');
        $data = $this->request->param('');
        $valid = new Validate($this->_rule);
        if(!$valid->check($data)){
            $this->error($valid->getError());
        }
        if ($id) {
            $info = AdminModel::get($id);
            $ret = $info->update($data, ['id' => $id]);
        } else {
            $info = new AdminModel();
            $info->data($data);
            $info->password = md5($this->request->param('password'));
            $ret = $info->save();
        }
        if ($ret) {
            return $this->success('操作成功', url('userlist'));
        } else {
            $this->error('操作失败，请稍后再试');
        }
    }

    /**
     * 修改昵称初始化
     */
    public function updateNickname()
    {
        // $nickname = db('administrator')->getFieldByUid($this->uid, 'nickname');
        $nickname = db('administrator')->where('id', $this->uid)->value('nickname');
        $this->assign('nickname', $nickname);
        // $this->meta_title = '修改昵称';
        return $this->fetch();
    }

    /**
     * 修改昵称提交
     */
    public function submitNickname()
    {
        //获取参数
        $nickname = input('post.nickname');
        empty($nickname) && $this->error('请输入昵称');
        // $password = input('post.password');
        // empty($password) && $this->error('请输入密码');

        $res = db('administrator')->where(array('id' => $this->uid))->update(['nickname' => $nickname]);

        if ($res !== false) {
            // $userInfo = \Think\Session::get('userInfo');
            // $userInfo['nickname'] = $nickname;
            \Think\Session::set('userInfo.nickname', $nickname);
            return $this->success('修改昵称成功！');
        } else {
            $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     */
    public function updatePassword()
    {
        // $this->meta_title = '修改密码';
        return $this->fetch();
    }

    public function updatePassword_admin()
    {
        if ($this->request->isPost()) {
            $uid = input('uid');
            $newPassword = input('password');
            $rePassword = input('repassword');
            if ($newPassword != $rePassword) {
                $this->error('两次输入密码不相同');
            }
            $ret = (new AdminModel)->resetPassword($this->uid, $newPassword);
            if ($ret) {
                return $this->success('重置用户密码成功');
            } else {
                $this->error('重置用户密码失败');
            }
        } else {
            return $this->fetch();
        }
    }

    /**
     * 修改密码提交
     */
    public function submitPassword()
    {
        //获取参数
        $password = input('post.old');
        empty($password) && $this->error('请输入原密码');
        $newPwd = input('post.password');
        empty($newPwd) && $this->error('请输入新密码');
        $repassword = input('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if ($newPwd !== $repassword) {
            $this->error('您输入的新密码与确认密码不一致');
        }

        if(! ((new AdminModel)->checkPassword($this->uid, $password)) ){
            $this->error('原密码不正确');
        }

        $res = (new AdminModel)->resetPassword($this->uid, $newPwd);
        if ($res) {
            return $this->success('修改密码成功！');
        } else {
            $this->error('修改失败');
        }
    }
}
