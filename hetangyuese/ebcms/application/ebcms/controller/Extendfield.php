<?php
namespace app\ebcms\controller;
class Extendfield extends \app\ebcms\controller\Common
{
    
    public function add()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                $data = input();
                $data['config'] = json_encode([]);
                \think\Db::name('extendfield') -> insert($data);
            });
            $this -> success('操作成功！');
        }
    }

    public function edit()
    {
        if (request()->isGet()) {
            $data = \think\Db::name('extendfield') -> find(input('id'));
            $data['config'] = json_decode($data['config'],true);
            if (input('do') == 'config') {
                return \ebcms\Form::fetch($data, array('formname' => $data['type']));
            } else {
                return \ebcms\Form::fetch($data);
            }
        } elseif (request()->isPost()) {
            \think\Db::transaction(function(){
                $data = input();
                if (isset($data['config'])) {
                    $data['config'] = json_encode($data['config']);
                }
                \think\Db::name('extendfield') -> update($data);
            });
            $this -> success('操作成功！');
        }
    }

    public function status(){
        \think\Db::transaction(function(){
            \think\Db::name('extendfield') -> where(['id'=>['in',input('ids')]]) -> setField('status',input('value')?1:0);
        });
        $this -> success('操作成功！');
    }

    public function resort(){
        \think\Db::transaction(function(){
            \think\Db::name('extendfield') -> where('id',input('id')) -> setField('sort',input('value'));
        });
        $this -> success('操作成功！');
    }

    public function delete()
    {
        \think\Db::transaction(function(){
            \think\Db::name('extendfield') -> where(['id'=>['in',input('ids')]]) -> delete();
        });
        $this -> success('删除成功！');
    }
}