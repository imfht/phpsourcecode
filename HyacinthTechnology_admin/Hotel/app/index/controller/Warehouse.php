<?php
namespace app\index\controller;

/*
 * 仓库管理
 *
 * */

class Warehouse extends Basics
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
     * 采购商品
     *
     * */
    public function index()
    {
        return view();
    }

    /*
     * 入库登记
     * */
    public function welcome()
    {
//        send_sms();
        return '欢迎使用bool系统';

    }

}
