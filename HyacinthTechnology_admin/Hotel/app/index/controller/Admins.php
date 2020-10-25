<?php
namespace app\index\controller;

use app\BaseController;



/*
 * 员工管理
 *
 * */

class Admins extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Admin';
        $this->new_model();
        $this->validate = new \app\index\validate\Admin();
        parent::initialize();
    }
    /*
     * 员工首页
     *
     * */
    public function index()
    {
        $list = $this->model->select_staff();
        return view('index',['list' => $list]);
    }

    /*
     * 添加员工
     * */
    public function adds(){
        if(request()->isAjax()){
            //验证字段
            if(!$this->checkDate(input('param.'))){
                return $this->return_json($this->validate->getError(),'0');
            }
            //添加数据
            return $this->model->add_plus();
        }
        $building = $this->select_all('building');
        return view('adds',['building' => $building]);
    }

    /*
     * 编辑员工
     * */
    public function edits(){
        $staff = $this->select_find('admin',['id'=>input('id')]);
        if(request()->isAjax()){
            //编辑数据
            $data = input('param.');

            if(empty($data['password'])){
                $data['password'] = $staff['password'];
            }else{
                $data['password'] = md5($data['password']);
            }

            if($this->model::update($data)){
                return $this->return_json('编辑成功','100');
            }else{
                return $this->return_json('编辑失败','0');
            }
        }
        $building = $this->select_all('building');
        $staff = $this->select_find('admin',['id'=>input('id')]);

        return view('edits',['building' => $building,'staff' => $staff]);
    }
}
