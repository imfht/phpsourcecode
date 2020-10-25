<?php
namespace app\ebcms\controller;
class Extend extends \app\ebcms\controller\Common
{
    
    public function index()
    {
        $tpl = '';
        if (input('tpl')) {
            $tpl = 'extend';
        }elseif (input('?id')) {
            $tpl = 'field';
        }
        return $this->fetch($tpl);
    }

    public function add()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                \think\Db::name('extend') -> insert(input());
            });
            $this -> success('操作成功！');
        }
    }

    public function edit()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch(\think\Db::name('extend')->find(input('id')));
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                \think\Db::name('extend') -> update(input());
            });
            $this -> success('操作成功！');
        }
    }

    public function resort(){
        \think\Db::transaction(function(){
            \think\Db::name('extend') -> where('id',input('id')) -> setField('sort',input('value'));
        });
        $this -> success('操作成功！');
    }

    public function delete()
    {
        \think\Db::transaction(function(){
            \think\Db::name('extend') -> where(['id'=>['in',input('ids')]]) -> delete();
            \think\Db::name('extendfield') -> where(['category_id'=>['in',input('ids')]]) -> delete();
        });
        $this -> success('操作成功！');
    }

}