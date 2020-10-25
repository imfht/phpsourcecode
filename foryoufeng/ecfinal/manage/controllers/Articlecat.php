<?php
/**
 * 品牌
 * Created by PhpStorm.
 * User: root
 * Date: 7/15/16
 * Time: 11:12 AM
 */
class Articlecat extends Admin{
    protected $modelName='articlecat';

    /**
     * 获取数据
     * @return mixed
     */
    public function get_list(){
        $keywords = empty(I('keywords')) ? '' : trim(I('keywords'));
        return $this->model->all($keywords);
    }

    public function add(){
        //获取分类列表
        $cate = $this->model->get_cat_name();
        if($_POST){
            //获取数据
            $data['cat_name'] = trim(I('cat_name', ''));//文章名称
            $data['cat_desc'] = trim(I('cat_desc', ''));//文章描述
            $data['sort_order'] = trim(I('sort_order', '50'));//排序
            $data['show_in_nav'] = trim(I('show_in_nav', ''));//是否到首页
            $data['cat_type'] = trim(I('cat_type', ''));//所属分类
            $data['parent_id'] = trim(I('catname', ''));//分类ID
            //验证数据是否存在
            if(!$data['cat_name']||!$data['cat_type']||!$data['parent_id']){
                $this->message('数据不全');
            }
            $tmp = $this->model->show_name($data['cat_name']);
            if($tmp){
                $this->message('文章名称已存在');
            }else{
                $result = $this->model->add($data);
            }

            if($result){
                $this->redirect("?c=articlecat");//成功跳转文章界面
            }else{
                $this->message('添加失败');
            }
        }else{
            $this->assign('cate_name',$cate);
            $this->display('articlecat/articlecat_form.html');//如果不是POST提交则只加载界面
        }
    }

    public function edit(){
        //获取分类列表
        $id = intval(I('cat_id',''));//接受ID，用来修改数据
        $cate = $this->model->get_cat_name($id);
        if($_POST){
            //获取数据
            $data['cat_id'] = $id;
            $data['cat_name'] = trim(I('cat_name', ''));//文章名称
            $data['cat_desc'] = trim(I('cat_desc', ''));//文章描述
            $data['sort_order'] = trim(I('sort_order', '50'));//排序
            $data['show_in_nav'] = trim(I('show_in_nav', ''));//是否到首页
            $data['cat_type'] = trim(I('cat_type', ''));
            $data['parent_id'] = trim(I('catname', ''));
            //验证数据是否存在
            if(!$data['cat_name']||!$data['cat_type']||!$data['parent_id']){
                $this->message('数据不全');
            }
            $tmp = $this->model->show_edit_name($data);
            if($tmp){
                $this->message('文章名称已存在');
            }else{
                $result = $this->model->save($data);
            }
            if($result !== false){
                $this->redirect("?c=articlecat");
            }else{
                $this->message('修改失败');
            }
        }else{
            $data=$this->model->show($id);//查找数据库是否存在这条数据
            if(!$data){
                $this->message('没有数据');
            }
            $this->assign('cate_name',$cate);
            $this->assign('data',$data);
            $this->display('articlecat/articlecat_form.html');
        }
    }
    
    public function show_nav(){
        $this->model->show_nav();
    }
}
