<?php
namespace app\index\controller;

/*
 * 优惠活动
 * */

class Activity extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Activitys';
        $this->new_model();
        parent::initialize();
    }
    /*
     * 活动首页
     * */
    public function index()
    {
        $list = $this->model->select_plus('page');
        return view('index',['list' => $list]);
    }

    /*
     * 添加活动
     * */
    public function adds(){
        if(request()->isAjax()){
            //添加数据
            return $this->model->add_plus();
        }
        $list = $this->select_all('layout');
        return view('adds',['list' => $list]);
    }

}
