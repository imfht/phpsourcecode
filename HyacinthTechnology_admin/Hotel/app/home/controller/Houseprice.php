<?php
namespace app\home\controller;

use app\index\controller\Basics;
use think\facade\Db;
/*
 * 查看房价
 * */
class Houseprice extends Basics
{
    /*
     * 查看房价
     * */
    public function index()
    {
        $list =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit,c.monday')
            ->join('layout b','a.type_id = b.id')
            ->join('week c','c.layout_id = a.id')
            ->where('a.building_id',session('building_id'))
            ->select();
        return view('index',['list' => $list]);
    }

}
