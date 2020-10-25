<?php
namespace app\hotel\controller;

use app\BaseController;
use think\facade\Db;

class Index extends BaseController
{
    /*
     * 酒店首页
     * */
    public function index()
    {
        $list = Db::table('layout')->select();

        return view('index',['list' => $list]);
    }

    /*
     * 酒店首页
     * */
    public function hotels()
    {
        $list = Db::table('layout')->select();

        return view('hotels',['list' => $list]);
    }
}
