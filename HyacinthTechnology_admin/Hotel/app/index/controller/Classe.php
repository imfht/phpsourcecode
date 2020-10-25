<?php
namespace app\index\controller;

use app\BaseController;

use app\index\validate\Classes;
use think\facade\Db;

/*
 * 班次设置
 *
 * */
class Classe extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Classes';
        $this->new_model();
        $this->validate = new Classes();
        parent::initialize();
    }


}
