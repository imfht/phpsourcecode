<?php
namespace app\ebcms\controller;
class Oplog extends \app\ebcms\controller\Common
{
    
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    public function delete()
    {
        \think\Db::transaction(function(){
            \think\Db::name('oplog') -> where(['id'=>['in',input('ids')]]) -> delete();
        });
        $this -> success('操作成功！');
    }

    // 查看详细
    public function detail()
    {
        return $this->fetch();
    }
}