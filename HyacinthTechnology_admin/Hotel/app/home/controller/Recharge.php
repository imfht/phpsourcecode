<?php
namespace app\home\controller;

use app\index\controller\Basics;
use think\facade\Db;
/*
 * 充值优惠
 * */
class Recharge extends Basics
{
    /*
     * 充值优惠
     * */
    public function index()
    {
//        $list = $this->select_all('recharge');
        $list = Db::name('recharge')->where('building_id',session('building_id'))->paginate(10);
        return view('index',['list' => $list]);
    }

    /*
     * 添加充值优惠
     * */
    public function adds()
    {
        if(request()->isAjax()){
            $data = input('param.');
            $data['building_id'] = session('building_id');
            //判断是否添加成功
            if(Db::name('recharge')->insert($data)){
                return $this->return_json('新增成功','100');
            }else{
                return $this->return_json('新增失败','0');
            }
        }
        return view();
    }

}
