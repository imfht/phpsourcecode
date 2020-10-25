<?php
namespace app\ebcms\controller;
class Manager extends \app\ebcms\controller\Common
{
    
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    public function add()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){

                $data = input();
                $data['email'] = strtolower($data['email']);
                $data['password'] = \ebcms\Func::crypt_pwd($data['password'], $data['email']);

                \think\Db::name('manager') -> insert($data);
            });
            $this -> success('操作成功！');
        }
    }

    public function edit()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch(\think\Db::name('manager')->find(input('id')));
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                $data = [
                    'id'        =>  input('id'),
                    'nickname'  =>  input('nickname'),
                    'avatar'    =>  input('avatar'),
                ];
                $email = \think\Db::name('manager')->where('id',input('id')) -> value('email');
                if ($email !== \think\Config::get('super_admin')) {
                    $data['email'] = input('email');
                }
                \think\Db::name('manager') -> update($data);
            });
            $this -> success('操作成功！');
        }
    }

    public function status($id)
    {
        $email = \think\Db::name('manager')->where('id',input('id')) -> value('email');
        if ($email == \think\Config::get('super_admin')) {
            $this -> error('超级管理员不支持此操作！');
        }
        
        \think\Db::transaction(function(){
            \think\Db::name('manager') -> where('id',input('id')) -> setField('status', input('value')?1:0);
        });
        $this -> success('操作成功！');
    }

    public function delete($id)
    {
        $email = \think\Db::name('manager')->where('id',input('id')) -> value('email');
        if ($email == \think\Config::get('super_admin')) {
            $this -> error('超级管理员不支持此操作！');
        }

        \think\Db::transaction(function(){
            \think\Db::name('manager') -> where('id',input('id')) -> delete();
            \think\Db::name('auth_access') -> where('uid',input('id')) -> delete();
        });
        $this -> success('操作成功！');
    }

    // 分配角色
    public function group()
    {

        $email = \think\Db::name('manager')->where('id',input('id')) -> value('email');
        if ($email == \think\Config::get('super_admin')) {
            $this -> error('超级管理员不支持此操作！');
        }

        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                // 移除老分组
                \think\Db::name('auth_access')->where(array('uid' => array('eq', input('id'))))->delete();

                // 重组新分组
                $group_ids = input('group_ids/a');
                if ($group_ids) {
                    $data = array();
                    foreach ($group_ids as $key => $value) {
                        $data[] = array(
                            'uid' => input('id'),
                            'group_id' => $value,
                        );
                    }
                    \think\Db::name('auth_access')->insertAll($data);
                }
            });
            $this -> success('操作成功！');
        }
    }

    // 重置密码
    public function password()
    {

        $email = \think\Db::name('manager') -> where('id',input('id')) -> value('email');
        if ($email == \think\Config::get('super_admin')) {
            $this -> error('超级管理员不支持此操作！');
        }

        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            // 重置密码
            \think\Db::transaction(function() use($email){
                $password = \ebcms\Func::crypt_pwd(input('password'), $email);
                \think\Db::name('manager') -> where('id',input('id')) -> setField('password', $password);
            });
            $this->success('密码已经重置！请尽快修改密码！谢谢');
        }
    }

    // 显示用户信息
    public function info()
    {
        return $this->fetch();
    }

}