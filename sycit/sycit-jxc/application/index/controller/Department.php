<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/8/24
// +----------------------------------------------------------------------
// | Title:  Department.php
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\index\model\AuthGroup;
use app\index\model\AuthGroupAccess;
use app\index\model\AuthRule;
use think\Db;
use think\Request;
use think\Session;
use think\Url;

class Department extends Common_base
{
    public function index() {
        //$authGroupM = new AuthGroup();
        $group = Db::name('auth_group')->order('id', 'asc')->select();

        foreach ($group as $kcou=>$vcou) {
            $group[$kcou]['count'] = Db::name('users')->where('user_auth',$vcou['id'])->where('status',1)->count();
        }
        foreach ($group as $key=>$val) {
            $group[$key]['son']=Db::name('users')->field(['id'=>'uid','user_name','user_nick','user_auth'])->where('user_auth',$val['id'])->where('status',1)->order('uid','asc')->select();
        }
        $this->assign('title', '部门管理');
        $this->assign('group', $group);
        //$this->assign('users', $users);
        return $this->fetch();
        //p($group);
        //p($users);
    }

    //
    public function add() {
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        return $this->fetch();
    }

    //增加
    public function add_do() {
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        if ($Request->isPost()) {
            $title = $Request->param('bumenname');
            if (empty($title)) {
                $this->error('有错误');
            }
            $ByTitle = Db::name('auth_group')->where('title', $title)->find();
            if ($ByTitle) {
                $this->error('名称已存在，请更换。');
            }
            $result = Db::name('auth_group')->insert(['title'=>$title]);
            if ($result) {
                $this->success('添加部门成功',Url::build('department/index'));
            } else {
                $this->error('添加部门数据错误');
            }
        }
    }

    //查询
    public function check_name() {
        $Request = Request::instance();
        if ($Request->isPost()) {
            $title = $Request->param('bumenname');
            $result = Db::name('auth_group')->where('title', $title)->find();
            if ($result) {
                return false;
            } else {
                return true;
            }
        }
    }

    //部门授权
    public function auth() {
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        // 检查是否有权限
        if (Session::get('user_auth.id') !==1) {
            $this->error("无权限操作");
        }
        $AuthGroup = new AuthGroup();
        $AuthGroupAccess = new AuthGroupAccess();
        $AuthRule = new AuthRule();
        //
        if ($Request->isPost()) {
            //提交操作
        } else {
            return $this->fetch();
        }
    }

    //权限添加
    public function auth_rule() {
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        // 检查是否有权限
        if (Session::get('user_auth.id') !==1) {
            $this->error("无权限操作");
        }
        $AuthRule = new AuthRule();
        if ($Request->isPost()) {
            //提交操作
        } else {
            return $this->fetch();
        }
    }
}