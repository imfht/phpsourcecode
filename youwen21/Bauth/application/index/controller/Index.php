<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return \think\View::instance()->fetch();
    }
}
