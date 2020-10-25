<?php
namespace app\home\controller;

use app\index\controller\Basics;
use think\facade\Db;
/*
 * 票据管理
 * */
class Invoice extends Basics
{
    /*
     * 退房票据
     * */
    public function refund_bill()
    {
        dump(input('id'));
        $list =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit,c.name,c.price as a_price,d.storey')
            ->join('layout b','a.type_id = b.id')
            ->join('activitys c','a.activity_id = c.id')
            ->join('storey d','a.storey_id = d.id')
            ->where(['a.id' => input('id')])
            ->select();
        //查询随客数据
        $follow =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit')
            ->join('layout b','a.type_id = b.id')
            ->where(['a.room_id' => input('id')])
            ->select();
        //计算总价格
        $price =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit')
            ->join('layout b','a.type_id = b.id')
            ->where(['a.room_id' => input('id')])
            ->sum('b.price');
        //计算总定金
        $deposit =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit')
            ->join('layout b','a.type_id = b.id')
            ->where(['a.room_id' => input('id')])
            ->sum('b.deposit');
        dump($follow);
        dump($price);
        return view('refund_bill',['list' => $list,'follow'=> $follow,'price' => $price,'deposit' => $deposit]);
    }

    /*
     * 入住单据和收费
     * */
    public function bill(){
//        $list = $this->select_find('room',['id' => input('id')]);
        $list =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit,c.name,c.price as a_price,d.storey')
            ->join('layout b','a.type_id = b.id')
            ->join('activitys c','a.activity_id = c.id')
            ->join('storey d','a.storey_id = d.id')
            ->where(['a.id' => input('id')])
            ->select();
        //查询随客数据
        $follow =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit')
            ->join('layout b','a.type_id = b.id')
            ->where(['a.room_id' => input('id')])
            ->select();
        //计算总价格
        $price =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit')
            ->join('layout b','a.type_id = b.id')
            ->where(['a.room_id' => input('id')])
            ->sum('b.price');
        //计算总定金
        $deposit =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit')
            ->join('layout b','a.type_id = b.id')
            ->where(['a.room_id' => input('id')])
            ->sum('b.deposit');
        dump($list);
        dump($follow);
        dump($price);
        return view('bill',['list' => $list,'follow'=> $follow,'price' => $price,'deposit' => $deposit]);
    }
    /*
     * 入住票据
     * */
    public function into_house(){
        $id = input('id');
        $map = "a.status = 1 AND (b.id={$id} OR b.room_id={$id})";
        $list =  Db::table('income')
            ->alias('a')
            ->field('a.*,b.room_num')
            ->join('room b','a.room_id = b.id')
            ->where($map)
            ->select();
        echo  Db::table('income')->getLastSql();
        dump($list);
        return view('into_house',['list' =>$list]);
    }


}
