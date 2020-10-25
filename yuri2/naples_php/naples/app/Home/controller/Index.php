<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/30
 * Time: 0:05
 */

namespace naples\app\Home\controller;


use naples\lib\base\Controller;

class Index extends Controller
{

    function index(){
        config('show_debug_btn',false);
        return $this->render();
    }
}