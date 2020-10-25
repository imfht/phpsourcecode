<?php
namespace app\index\controller;

use app\BaseController;
use app\index\validate\Building;
use think\facade\Db;
/*
 * 楼栋设置
 *
 * */

class Buildings extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Building';
        $this->new_model();
        $this->validate = new Building();
        parent::initialize();
    }


}
