<?php
namespace app\home\controller;


use app\index\controller\Basics;
use app\index\validate\Goodss;
use think\facade\Db;


/*
 * 商品模型
 *
 * */

class Goods extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Goodss';
        $this->new_model();
        $this->validate = new Goodss();
        parent::initialize();
    }

    /*
     * 显示页面
     *
     * */
    public function index()
    {
        $list = Db::table('goodss')->where('building_id',session('building_id'))->paginate(10);

        return view('index',['list' => $list]);
    }

    /*
     * 查看所有商品
     * */
    public function select_goods(){
        $list =  Db::table('goodss')->where('building_id',session('building_id'))->paginate(10);
//        echo Db::table('purchases')->getLastSql();
        return view('select_goods',['list' => $list]);
    }



}
