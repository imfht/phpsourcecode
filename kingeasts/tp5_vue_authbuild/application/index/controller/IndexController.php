<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/11/1
 * Time: 9:34
 */

namespace app\index\controller;


use think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->fetch('/index');
    }
}
