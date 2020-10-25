<?php
namespace app\index\controller;

use app\BaseController;



/*
 * 首页
 *
 * */

class Index extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Admin';
        $this->new_model();
        parent::initialize();
    }
    /*
     * 酒店系统首页
     *
     * */
    public function index()
    {
        return view();
    }

    /*
     * 欢迎界面
     * */
    public function welcome()
    {
//        send_sms();
        return view();

    }
}
