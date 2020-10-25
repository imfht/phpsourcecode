<?php

namespace app\admin\controller;

use app\admin\model\GoodcateModel;
use think\Controller;
use think\Request;

class Goodcate extends Base
{
    protected $title="商城管理";
    public function index()
    {
        $name = "商品分类";
        $this->assign([
            'name' => $name,
            'title' => $this->title,

        ]);
        $this->assign('title',$this->title);
        return $this->fetch();
   }

    public function catelist(){
        $goodcate = new GoodcateModel();
        $list = $goodcate->listAll();
        return json($list);
    }


    /**
     * 添加分类
     */
    public function add($id){

        $name = "添加商品分类";
        $goodcate = new GoodcateModel();
        if(request()->isPost()){
            $data = input('post.');

            $sure = $goodcate->add($data);
            if($sure['code']=1){
                $this->ky_success($sure['msg'],$sure['data']);
            }else{
                $this->ky_error($sure['msg']);
            }
        }else{
            if($id==0){
                $list=array('cate_id'=>'0','cate_name'=>'根目录');
            }else{
                $list = $goodcate->findById($id);
            }

            $this->assign([
                'pmenu'=>$list,
                'name'=>$name
            ]);

            return $this->fetch();
        }

    }



    public function edit($id){

        $name = "修改商品分类";
        $goodCate = new GoodcateModel();
        if(request()->isPost()) {
            $data = input('post.');
            $data['cate_id'] = $id;
            $reuslt = $goodCate->edit($data);
            if ($reuslt['code'] == 1) {
                $this->ky_success($reuslt['msg'], $reuslt['data']);
            } else {
                $this->ky_error($reuslt['msg']);
            }
        }else{
            $list = $goodCate->findByIdAll($id);
            $lists = $goodCate->findById($list['parent_id']);
            $this->assign([
                'pmenu'=>$lists,
                'vo'=>$list,
                'name'=>$name
            ]);
           return $this->fetch();
        }
    }





    public function del($id){
        $goodCate = new GoodcateModel();
        $res = $goodCate->del($id);
        return json(['code'=>$res['code'],'data'=>$res['data'],'msg'=>$res['msg']]);

    }


    public function catetree(){
        $goodcate = new GoodcateModel();
        $tree = $goodcate->bulidTree();
        return $tree;
    }



}
