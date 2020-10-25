<?php
namespace app\apply\controller;

use app\index\controller\Basics;
use think\facade\Db;

/*
 * 语音插件
 * */
class Voice extends Basics
{
    public function index()
    {
        $list = Db::name('voice')->where('id','1')->find();
        return view('index',['list' => $list]);
    }

    /*
     * 修改数据
     * */
    public function edits(){
        if(request()->isPost()){

            if(Db::name('voice')->where('id','1')->update(input('param.'))){
                return $this->return_json('操作成功','100');
            }else{
                return $this->return_json('操作失败','0');
            }
        }
    }
}
