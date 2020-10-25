<?php
namespace app\ebcms\controller;
class Group extends \app\ebcms\controller\Common
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
                \think\Db::name('auth_group') -> insert(input());
            });
            $this -> success('操作成功！');
        }
    }

    public function edit($id)
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch(\think\Db::name('auth_group') -> find(input('id')));
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                \think\Db::name('auth_group') -> update(input());
            });
            $this -> success('操作成功！');
        }
    }

    public function resort(){
        \think\Db::transaction(function(){
            \think\Db::name('auth_group') -> where('id',input('id')) -> setField('sort',input('value'));
        });
        $this -> success('操作成功！');
    }

    public function status()
    {
        \think\Db::transaction(function(){
            \think\Db::name('auth_group') -> where(['id'=>['in',input('ids')]]) -> setField('status',input('value')?1:0);
        });
        $this -> success('操作成功！');
    }

    public function delete()
    {
        \think\Db::transaction(function(){
            \think\Db::name('auth_group') -> where(['id'=>['in',input('ids')]]) -> delete();
            \think\Db::name('auth_access') -> where(['group_id'=>['in',input('ids')]]) -> delete();
        });
        $this -> success('操作成功！');
    }

    public function rule()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                \think\Db::name('auth_group')->where(array('id' => array('eq', input('id'))))->setField('rules', implode(',', input('rule_ids/a',[])));
            });
            $this -> success('操作成功！');
        }
    }

    // 功能菜单分配
    public function menu()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                \think\Db::name('auth_group')->where(array('id' => array('eq', input('id', 0, 'intval'))))->setField('menus', implode(',', input('menu_ids/a',[])));
            });
            $this -> success('操作成功！');
        }
    }
}