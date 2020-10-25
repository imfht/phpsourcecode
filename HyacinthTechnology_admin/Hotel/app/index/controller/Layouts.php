<?php
namespace app\index\controller;

use app\BaseController;
use app\index\validate\Layout;

/*
 * 房型设置
 *
 * */

class Layouts extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Layout';
        $this->new_model();
        $this->validate = new Layout();
        parent::initialize();
    }

    /*
     * 房型首页
     * */
/*    public function index()
    {
        $list = $this->model->select_plus('page');
        return view('index',['list' => $list]);
    }*/

    /*
     * 添加房型
     * */
/*    public function adds(){
        if(request()->isAjax()){
            //验证字段
            if(!$this->checkDate(input('param.'))){
                return $this->return_json($this->validate->getError(),'0');
            }
            //添加数据
            return $this->model->add_plus();
        }
        return view();
    }*/

    /*
     * 编辑房型
     * */
/*    public function edits(){
        $list = $this->select_find(strtolower($this->model_name),['id' => input('id')]);

        if(request()->isAjax()){
            //编辑数据
            return $this->model->edit_plus();
        }
        return view('edits',['list' => $list]);
    }*/

}
