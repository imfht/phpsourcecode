<?php
namespace app\ebcms\controller;
class Nav extends \app\ebcms\controller\Common
{
    
    public function index()
    {
        return $this->fetch();
    }

    public function add()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                $data = input();
                $data['eb_ext'] = json_encode($data['eb_ext']);
                \think\Db::name('nav') -> insert($data);
            });
            $this -> success('操作成功！');
        }
    }

    public function edit()
    {
        if (request()->isGet()) {
            $data = \think\Db::name('nav')->find(input('id'));
            $data['eb_ext'] = json_decode($data['eb_ext'],true);
            return \ebcms\Form::fetch($data);
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                $data = input();
                $data['eb_ext'] = json_encode($data['eb_ext']);
                \think\Db::name('nav') -> update($data);
            });
            $this -> success('操作成功！');
        }
    }

    public function resort(){
        \think\Db::transaction(function(){
            \think\Db::name('nav') -> where('id',input('id')) -> setField('sort',input('value'));
        });
        $this -> success('操作成功！');
    }

    public function status(){
        \think\Db::transaction(function(){
            \think\Db::name('nav') -> where(['id'=>['in',input('ids')]]) -> setField('status',input('value')?1:0);
        });
        $this -> success('操作成功！');
    }
    
    public function delete()
    {
        \think\Db::transaction(function(){
            \think\Db::name('nav') -> where(['id'=>['in',input('ids')]]) -> delete();
        });
        $this -> success('操作成功！');
    }

}