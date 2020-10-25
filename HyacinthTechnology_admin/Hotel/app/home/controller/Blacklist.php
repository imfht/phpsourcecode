<?php
namespace app\home\controller;

use app\index\controller\Basics;
use think\facade\Db;
/*
 * 黑名单管理
 * */
class Blacklist extends Basics
{
    /*
     * 黑名单
     * */
    public function index()
    {
//        $list = $this->select_all('blacklist');
        $list = Db::name('blacklist')->where('building_id',session('building_id'))->paginate(10);
        return view('index',['list' => $list]);
    }

    /*
     * 添加黑名单
     * */
    public function adds()
    {
        if(request()->isAjax()){
            $data = input('param.');
            $data['create_time'] = time();
            $data['building_id'] = session('building_id');
            //判断是否添加成功
            if(Db::name('blacklist')->insert($data)){
                return $this->return_json('新增成功','100');
            }else{
                return $this->return_json('新增失败','0');
            }
        }
        return view();
    }

}
