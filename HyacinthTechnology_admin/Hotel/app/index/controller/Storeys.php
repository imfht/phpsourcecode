<?php
namespace app\index\controller;

use app\BaseController;
use app\index\validate\Storey;
use think\facade\Db;

/*
 * 楼层设置
 *
 * */
class Storeys extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Storey';
        $this->new_model();
        $this->validate = new Storey();
        parent::initialize();
    }

    /*
     * 楼层设置
     *
     * */
/*    public function index()
    {
        $list = $this->model->select_plus('page');
        return view('index',['list' => $list]);
    }*/

}
