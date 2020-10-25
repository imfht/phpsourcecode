<?php
namespace app\index\controller;

use app\BaseController;
use app\index\validate\Room;

/*
 * 收费方式
 *
 * */

class Charges extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Charge';
        $this->new_model();
        parent::initialize();
    }

    /*
     * 普通用户
     * */
    public function index()
    {
        $list = $this->select_find('charge',['username' => 'ordinary']);
        return view('index',['list' => $list]);
    }

    /*
     * 酒店会员
     * */
    public function vip(){
        $list = $this->select_find('charge',['username' => 'vip']);
        return view('index',['list' => $list]);
    }


}
