<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;

class Index extends Common
{
    public function index()
    {
        $mysql = Db::query("select VERSION() as mysql");
        $t = time()-3600*24*60;
        Db::name('log')->where("t < $t")->delete();//删除60天前的日志

        $list = Db::name('log')->order('id desc')->paginate(25);
        View::assign('list', $list);

        View::assign('mysql', $mysql[0]['mysql']);
        return View::fetch();
    }
}
