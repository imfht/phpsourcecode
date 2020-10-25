<?php
/*
 * 2017年1月18日 星期三
 * 接口调试工具2
*/
namespace app\geek\controller;
use think\Controller;
class Apibin extends Controller
{
    public function index()
    {
        $data = count($_POST)>0? $_POST:$_GET;
        // println($data);
        $data = json($data);
        return $data;
    }
}