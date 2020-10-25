<?php
namespace app\index\controller;

use app\BaseController;
use app\index\validate\Room;

/*
 * 房间信息
 *
 * */

class Rooms extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Room';
        $this->new_model();
        $this->validate = new Room();
        parent::initialize();
    }

    /*
     * 房间首页
     * */
    public function index()
    {
        if(request()->isPost()){
            $data = input('param.');
            //查询条件
            if(empty($data['username'])){
                $map = [
                    'b.id'=>$data['building_id']
                ];
            }else if(!empty($data['username']) && $data['building_id'] != '0'){
                $map = [
                    'b.id' => $data['building_id'],
                    'a.room_num' => $data['username']
                ];
            }else{
                $map = [
                    'a.room_num' => $data['username']
                ];
            }

            //查询数据
            if($data['building_id'] != '0'){
                $list = $this->model->where_room($map);
            }else if(!empty($data['username'])){
                $list = $this->model->where_room($map);
            }else{
                $list = $this->model->select_room();
            }

        }else{
            $list = $this->model->select_room();
        }
        $building = $this->select_all('building');
        return view('index',['list' => $list,'building' => $building]);
    }

    /*
     * 添加房间
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
        $storey = $this->select_all('storey');
        $layout = $this->select_all('layout');
        return view('adds',['building' => $building,'storey' => $storey,'layout' => $layout]);
    }

    /*
     * 编辑房间
     * */
    public function edits(){
//        $list = $this->select_find('layout',['id' => input('id')]);
        $list = $this->model->find_room(['a.id' => input('id')]);
//        dump($list);
        if(request()->isAjax()){
            //编辑数据
            return $this->model->edit_plus();
        }
        $building = $this->select_all('building');
        $storey = $this->select_all('storey');
        $layout = $this->select_all('layout');
        return view('edits',['building' => $building,'storey' => $storey,'layout' => $layout,'list' => $list]);
    }

}
