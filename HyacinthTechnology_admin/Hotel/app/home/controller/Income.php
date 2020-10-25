<?php
namespace app\home\controller;

use app\index\controller\Basics;
use think\facade\Db;
/*
 * 营业查询
 * */
class Income extends Basics
{
    /*
     * 收入明细
     * */
    public function index()
    {
//        $list = Db::table('income')->paginate('10');
        $list =  Db::table('income')
            ->alias('a')
            ->field('a.*,b.room_num')
            ->join('room b','a.room_id = b.id')
            ->where('b.building_id',session('building_id'))
            ->paginate('10');
        return view('index',['list' => $list]);
    }

    /*
     * 商品消费
     * */
    public function goods_shop(){
        $data =  Db::table('consume')
            ->alias('a')
            ->field('a.*,b.room_num,d.number,d.name,d.price')
            ->join('room b','a.room_id = b.id')
            ->join('goodss d','a.goods_id = d.id')
            ->where('d.building_id',session('building_id'))
            ->paginate(10);
        dump($data);
        return view('goods_shop',['data' => $data]);
    }


}
