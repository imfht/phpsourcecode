<?php
namespace app\center\controller;
use think\Loader;
use think\Controller;
class Uinfo extends Controller
{
    public function index()
    {
        echo rand(100000,999999);
        /*
        echo $this->view->v_join;
        debugOut($this->view,true);
        */
    }
}